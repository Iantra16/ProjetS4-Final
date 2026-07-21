
# Prep V3 — bouts de code par hypothèse

⚠️ Ce sont des **hypothèses**, pas le sujet réel. Le but n'est pas de tout précoder
à l'avance (vous risquez de devoir défaire), mais d'avoir un modèle en tête pour
aller vite si l'un de ces axes tombe. Adaptez les noms de table/colonne à votre
schéma réel avant de coller quoi que ce soit.

---

## 1. Plafonds / quotas (journalier ou mensuel)

**Migration**

```php
// AlterOperateurAddPlafonds.php
public function up()
{
    $this->forge->addColumn('type_operation', [
        'plafond_jour' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
    ]);
}
```

**Model — vérifier le cumul du jour avant d'enregistrer**

```php
// OperationModel.php
public function totalDuJour(int $idNumero, int $idTypeOperation): float
{
    return (float) $this->selectSum('montant')
        ->where('id_numero', $idNumero)
        ->where('id_type_operation', $idTypeOperation)
        ->where('DATE(created_at)', date('Y-m-d'))
        ->get()->getRow()->montant ?? 0.0;
}
```

**Controller — dans `enregistrer()`, avant de créer l'opération**

```php
$typeOp   = $this->typeOperationModel->find($idTypeOperation);
$dejaFait = $this->operationModel->totalDuJour($numero['id'], $idTypeOperation);

if ($typeOp['plafond_jour'] !== null && ($dejaFait + $montant) > $typeOp['plafond_jour']) {
    return redirect()->back()->with('error',
        'Plafond journalier dépassé (' . $typeOp['plafond_jour'] . ' Ar max).');
}
```

---

## 2. Rôle "agent" (dépôt/retrait pour le compte d'un client)

**Migration**

```php
public function up()
{
    $this->forge->addField([
        'id'          => ['type' => 'INT', 'auto_increment' => true],
        'nom'         => ['type' => 'VARCHAR', 'constraint' => 100],
        'numero_agent'=> ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
        'mot_de_passe'=> ['type' => 'VARCHAR', 'constraint' => 255],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('agent');
}
```

**Controller — un login séparé, puis réutiliser la logique d'opération existante**

```php
// AgentController.php
public function enregistrerPourClient()
{
    if (! session()->get('agent_id')) {
        return redirect()->to('/agent/login');
    }

    $numeroClient = $this->request->getPost('numero_client');
    $numero = $this->numeroTelephoneModel->trouverParNumero($numeroClient);
    if (! $numero) {
        return redirect()->back()->with('error', 'Client introuvable.');
    }

    // Réutilise exactement la même méthode que le client utiliserait lui-même
    return $this->operationController->enregistrerPourNumero($numero['id'], $this->request->getPost());
}
```

> Idée clé : ne dupliquez pas la logique de calcul de frais — extrayez-la de
> `OperationController::enregistrer()` dans une méthode `enregistrerPourNumero($idNumero, $data)`
> réutilisable par le client ET par l'agent.

---

## 3. Annulation / remboursement d'une opération récente

**Migration**

```php
$this->forge->addColumn('operation', [
    'annulee' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
]);
```

**Model**

```php
public function peutEtreAnnulee(array $operation): bool
{
    // Exemple de règle : annulable dans les 10 minutes suivant sa création
    return ! $operation['annulee']
        && strtotime($operation['created_at']) > (time() - 600);
}
```

**Controller**

```php
public function annuler($idOperation)
{
    $op = $this->operationModel->find($idOperation);

    if (! $op || ! $this->operationModel->peutEtreAnnulee($op)) {
        return redirect()->back()->with('error', 'Opération non annulable.');
    }

    $this->db->transStart();

    // Inverse le mouvement de solde
    $this->soldeModel->ajuster($op['id_numero'], $op['type'] === 'depot' ? -$op['montant'] : $op['montant']);
    $this->operationModel->update($idOperation, ['annulee' => 1]);

    $this->db->transComplete();

    return redirect()->back()->with('success', 'Opération annulée.');
}
```

---

## 4. Code PIN de confirmation

**Migration**

```php
$this->forge->addColumn('numero_telephone', [
    'pin' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // hashé
]);
```

**Model**

```php
public function verifierPin(int $idNumero, string $pinSaisi): bool
{
    $numero = $this->find($idNumero);
    return $numero['pin'] && password_verify($pinSaisi, $numero['pin']);
}

public function definirPin(int $idNumero, string $pin): bool
{
    return $this->update($idNumero, ['pin' => password_hash($pin, PASSWORD_DEFAULT)]);
}
```

**Controller — avant toute opération sensible**

```php
if (! $this->numeroTelephoneModel->verifierPin($numeroId, $this->request->getPost('pin'))) {
    return redirect()->back()->with('error', 'Code PIN incorrect.');
}
```

---

## 5. Compte bloqué / suspendu par l'opérateur

**Migration**

```php
$this->forge->addColumn('numero_telephone', [
    'statut' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'actif'], // actif | bloque
]);
```

**Controller admin**

```php
public function bloquer($idNumero)
{
    $this->numeroTelephoneModel->update($idNumero, ['statut' => 'bloque']);
    return redirect()->back()->with('success', 'Compte bloqué.');
}
```

**Filtre côté client — au login ou avant chaque opération**

```php
if ($numero['statut'] === 'bloque') {
    return redirect()->to('/client/login')->with('error', 'Compte suspendu, contactez l’opérateur.');
}
```

> Le plus propre : créer un `Filter` CI4 (`app/Filters/CompteActifFilter.php`) appliqué
> sur toutes les routes `client/*` plutôt que de recopier ce `if` dans chaque méthode.

---

## 6. Export CSV des rapports

```php
// RapportController.php
public function exporterGainsCsv()
{
    $donnees = $this->operationModel->gainsParOperateur();

    $this->response->setHeader('Content-Type', 'text/csv');
    $this->response->setHeader('Content-Disposition', 'attachment; filename=gains.csv');

    $out = fopen('php://output', 'w');
    ob_start();
    fputcsv($out, ['Opérateur', 'Type', 'Total frais']);
    foreach ($donnees as $ligne) {
        fputcsv($out, [$ligne['operateur'], $ligne['type'], $ligne['total']]);
    }
    fclose($out);

    return $this->response->setBody(ob_get_clean());
}
```

---

## 7. Pagination / recherche sur l'historique

**Model — CI4 a la pagination native, pas besoin de tout réécrire**

```php
public function historiquePagine(int $idNumero, ?string $recherche = null)
{
    $builder = $this->where('id_numero', $idNumero);

    if ($recherche) {
        $builder->groupStart()
            ->like('type', $recherche)
            ->orLike('montant', $recherche)
            ->groupEnd();
    }

    return $builder->orderBy('created_at', 'DESC')->paginate(10);
}
```

**Controller**

```php
public function historique()
{
    $recherche = $this->request->getGet('q');
    $data['operations'] = $this->operationModel->historiquePagine(session()->get('numero_id'), $recherche);
    $data['pager']      = $this->operationModel->pager;

    return view('Front/historique', $data);
}
```

**Vue**

```php
<?= $pager->links() ?>
```

---

## 8. Validation stricte des entrées

```php
// Règles CI4 réutilisables, à mettre dans le Controller ou un Validation config
$rules = [
    'montant' => 'required|numeric|greater_than[0]',
    'numero_destinataire' => 'required|regex_match[/^0[0-9]{9}$/]',
];

if (! $this->validate($rules)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

**Cas limites à tester vous-mêmes avant que le prof ne le fasse**

- montant = 0 ou négatif
- montant exactement à la frontière d'une tranche (ex. 1000 vs 1001 Ar)
- numéro destinataire = numéro de l'expéditeur (auto-transfert)
- numéro destinataire inexistant
- solde insuffisant de peu (montant + frais > solde de 1 Ar)
- caractères spéciaux / injection dans les champs numéro et montant

---

## Priorité si le temps manque

1. Validation stricte (#8) — gratuit, rapide, couvre le plus de cas d'évaluation en direct
2. Compte bloqué (#5) et PIN (#4) — souvent demandés ensemble comme "sécurité"
3. Plafonds (#1) — réutilise directement votre logique de tranches existante
4. Pagination (#7) — CI4 le fait presque tout seul
5. Agent (#2), annulation (#3), export CSV (#6) — plus lourds, à ne traiter que si confirmés par le sujet réel
