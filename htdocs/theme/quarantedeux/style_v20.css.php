/**
 * Lists
 */
#id-right > .fiche > form[action*="list.php"] > div.div-table-responsive > table,
#id-right > .fiche > #dragDropAreaTabBar > form[action*="list.php"] > div.div-table-responsive > table{
    & > thead > tr > *:last-child,
    & > tbody > tr > td:last-child,
    & > tfoot > tr > td:last-child {
        position: relative;
        border-left: none;
        background: inherit !important; /* Important needed to overload the old implementation */
    }


    & > thead.sticky > tr > *:first-child,
    & > tbody > tr > td:first-child,
    & > tfoot > tr > td:first-child {
        position: sticky;
        left: 0;
        background: inherit;
        min-width: var(--width-actioncolumn);
    }

    & > thead.sticky > tr > *:first-child,
    & > tbody > tr.oddeven > td:first-child {
        padding: 0;
    }

    & > tbody > tr.liste_total,
    & > tfoot > tr.liste_total {
        background-color: var(--inputbackgroundcolor);
    }

    @media (min-width: 768px) {

        & > thead > tr > *:nth-child(2),
        & > tbody > tr > td:nth-child(2) {
            background: inherit;
        }

        & > thead > tr > *:nth-child(2),
        & > tbody > tr > *:nth-child(2),
        & > tfoot > tr > *:nth-child(2) {
            position: sticky;
            left: var(--width-actioncolumn);
        }

        & > thead.sticky > tr:first-child > *:nth-child(2) {
            z-index: 5;
        }

        & > thead.sticky > tr:nth-child(2) > *:nth-child(2) {
            z-index: 3;
        }

        & > tbody > tr > td:nth-child(2) {
            z-index: 1;
        }
    }
}
