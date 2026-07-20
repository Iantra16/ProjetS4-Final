<?php
/**
 * Icônes SVG inline — aucune police externe, aucune requête réseau.
 * Usage dans une vue : <?= icon('wallet') ?>  ou  <?= icon('wallet', 'me-1') ?>
 *
 * Pour ajouter une icône : ajouter une entrée dans le tableau $paths
 * (chemin SVG copié depuis n'importe quel set "line icons" 24x24, licence libre).
 */
if (! function_exists('icon')) {
    function icon(string $name, string $class = ''): string
    {
        $paths = [
            // navbar client
            'wallet'       => '<path d="M21 7H5a2 2 0 0 1-2-2 2 2 0 0 1 2-2h13v4Z"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M21 12a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h1v-5Z"/>',
            'operation'    => '<path d="m17 3 4 4-4 4"/><path d="M3 7h18"/><path d="m7 21-4-4 4-4"/><path d="M21 17H3"/>',
            'historique'   => '<path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l4 2"/>',
            'logout'       => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
            'login'        => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/>',
            'admin'        => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>',

            // sidebar admin
            'operateurs'   => '<rect x="3" y="10" width="18" height="10" rx="1"/><path d="M6 10V6a6 6 0 0 1 12 0v4"/>',
            'prefixes'     => '<path d="M9 5H2l7 7-7 7h7"/><path d="M20 5h-7l7 7-7 7h7"/>',
            'types'        => '<path d="m12 2 9 4.5-9 4.5-9-4.5Z"/><path d="m3 13.5 9 4.5 9-4.5"/><path d="m3 9 9 4.5 9-4.5"/>',
            'tranches'     => '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
            'comptes'      => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'gains'        => '<polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>',
            'envoyer'      => '<line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>',
            'menu'         => '<line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>',
            'check'        => '<polyline points="20 6 9 17 4 12"/>',
            'alert'        => '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        ];

        $d = $paths[$name] ?? '';
        if ($d === '') {
            return '';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" '
             . 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" '
             . 'stroke-linejoin="round" class="icon ' . esc($class, 'attr') . '" aria-hidden="true">'
             . $d . '</svg>';
    }
}