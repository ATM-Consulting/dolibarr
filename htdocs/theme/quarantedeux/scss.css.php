@charset "UTF-8";
:root {
  /* STRUCTURE */
  --aligncenter: calc(100% - 2rem);
  --column-gap: 1rem;
  --row-gap: 1rem;
  --width-secondary-menu: clamp(250px, 15vw, 300px);
  /* STYLES */
  /* – BORDER */
  --border-width: 1px;
  --border-style: solid;
  --border-color-light: color-mix(in srgb, currentColor 20%, transparent);
  /* – BORDER RADIUS */
  --border-radius--small: 0.25rem;
  --border-radius--medium: 0.5rem;
  --border-radius--large: 1rem;
  /* TODO */
  /* HISTORIC */
  --color_border_lines_h: solid 1px rgba(0, 0, 0, 0.15);
  --inner-lr: 2vw;
  --inner-tb: 0rem;
  /* TABLELINES */
  --cellwidth: 150px;
  /* V20 LISTINGS */
  --width-actioncolumn: 6em;
}

/* BREAKPOINTS */
/* 1 - Fonctions primaires */
/* BREAKPOINTS */
/* Detection de l'orientation de l'écran entre portrait ou paysage */
/* smartphones, touchscreens */
/* stylus-based screens */
/* Nintendo Wii controller, Microsoft Kinect */
/* mouse, touch pad */
/* at least one input mechanism of the device includes a pointing device of limited accuracy. */
/* at least one input mechanism of the device includes an accurate pointing device. */
/* the device does not include any pointing device. */
/* one or more available input mechanism(s) can hover over elements with ease */
/* one or more available input mechanism(s) can hover, but not with ease (for example simulating the hovering when performing a long touch) */
/* one or more available input mechanism(s) cannot hover or there are no pointing input mechanisms */
/* TEST */
/* TEST */ /* For overstyle all paginations... */
/* ... */
/*_root*/
/*... All files here */
div.tabBar {
  border-top: none;
  background: none;
  color: currentColor;
  margin-bottom: 0;
  padding-bottom: 0;
}

@media (min-width: 1024px) {
  .fichecenter > .fichetwothirdright {
    padding-left: var(--column-gap);
  }
}

.fiche > .table-fiche-title:first-of-type {
  margin-top: 1rem;
}
.fiche > form > .table-fiche-title:first-of-type {
  margin-top: 1rem;
}
/*DOLILOL V13*/
.fiche > img[src*="puce.png"] {
  width: 10px !important;
  height: 10px !important;
  display: none;
}

.fiche > img[src*=flags] {
  width: 2em !important;
  margin-left: 80px !important;
}

@media (orientation: landscape) {
  div.inline-block.floatleft.valignmiddle.maxwidth750.marginbottomonly.refid.refidpadding {
    width: 100%;
  }
  div.inline-block.floatleft.valignmiddle.maxwidth750.marginbottomonly.refid.refidpadding > div.refidno {
    width: 100%;
  }
  div.inline-block.floatleft.valignmiddle.maxwidth750.marginbottomonly.refid.refidpadding > div.refidno > form[action*="/card.php"] {
    width: 80%;
    display: inline-flex;
    align-items: end;
    gap: 0.5em;
  }
  div.inline-block.floatleft.valignmiddle.maxwidth750.marginbottomonly.refid.refidpadding > div.refidno > form[action*="/card.php"] > input[id^=ref_] {
    flex-grow: 6;
  }
  div.inline-block.floatleft.valignmiddle.maxwidth750.marginbottomonly.refid.refidpadding > div.refidno > form[action*="/card.php"] > input.button {
    flex-grow: 1;
  }
}
@media (orientation: portrait) {
  div.refidno > form[action*="/card.php"] > input[id^=ref_] {
    width: 100%;
  }
}
table#tablelines tr[id*=row-] {
  scroll-margin-top: 100px !important;
}

.prod_entry_mode_free span.select2,
.prod_entry_mode_predef span.select2 {
  min-width: 100px;
}
.prod_entry_mode_free input#search_idprod,
.prod_entry_mode_predef input#search_idprod {
  min-width: 300px;
}

tr.liste_titre input:not([type=checkbox], [type=submit]),
tr.liste_titre_filter input:not([type=checkbox], [type=submit]) {
  width: 100%;
}

#id-right > .fiche {
  height: auto;
}
#id-right > .fiche > form[action*="list.php"], #id-right > .fiche > .tabBar > form[action*="list.php"] {
  position: relative;
  width: 100%;
  max-width: 100%;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  height: 0px;
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title {
  width: 100%;
  padding: 1rem;
  display: block;
  margin: 0 auto;
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title > tbody, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title > tbody {
  width: 100%;
  display: block;
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title > tbody > tr, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title > tbody > tr {
  /*ROW*/
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  overflow-x: auto;
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title > tbody > tr > td, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title > tbody > tr > td {
  /*CELLS FOR GLOBAL STYLE*/
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title > tbody > tr > td.col-title, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title > tbody > tr > td.col-title {
  flex-grow: 1;
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title > tbody > tr > td.right, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title > tbody > tr > td.right {
  order: 2;
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title > tbody > tr > td.right div.pagination > ul, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title > tbody > tr > td.right div.pagination > ul {
  display: flex;
  flex-wrap: nowrap;
  align-items: baseline;
  gap: 1em;
}
#id-right > .fiche > form[action*="list.php"] .table-fiche-title > tbody > tr > td.right div.pagination > ul > li, #id-right > .fiche > .tabBar > form[action*="list.php"] .table-fiche-title > tbody > tr > td.right div.pagination > ul > li {
  padding: 0 !important;
  margin: 0 !important;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive {
  min-height: 50px;
  overflow-y: auto;
  overflow-x: auto;
  flex-grow: 1;
  width: 100%;
  position: relative;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive a, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive a {
  color: var(--colortext, black);
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive a:not(.reposition), #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive a:not(.reposition) {
  text-decoration: underline;
  font-weight: bold;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th {
  border-right: var(--border-width) var(--border-style) var(--border-color-light) !important;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre {
  padding-bottom: 5px;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre select,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre input,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre span[role*=textbox], #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre select,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre input,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre span[role*=textbox], #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre select,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre input,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre span[role*=textbox], #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre select,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre input,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre span[role*=textbox], #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre select,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre input,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre span[role*=textbox], #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre select,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre input,
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre span[role*=textbox], #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre select,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre input,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre span[role*=textbox], #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre select,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre input,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre span[role*=textbox], #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre select,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre input,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre span[role*=textbox], #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre select,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre input,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre span[role*=textbox], #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre select,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre input,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre span[role*=textbox], #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre select,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre input,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre span[role*=textbox] {
  background: var(--colorbackbody);
  border: var(--border-width) var(--border-style) var(--border-color-light);
  border-radius: var(--border-radius--medium);
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > td.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > tr > th.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > td.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > thead > tr > th.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > td.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]), #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tfoot > tr > th.liste_titre input:not([type=checkbox], [type=date], .hasDatepicker, [name=search_month]) {
  min-width: 100%;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th[class*=liste_titre] *, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th[class*=liste_titre] * {
  font-weight: bold !important;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th.liste_titre a, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th.liste_titre a {
  color: inherit;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th.liste_titre_sel span, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th.liste_titre_sel a, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th.liste_titre_sel span, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre th.liste_titre_sel a {
  color: var(--colortextlink);
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre_filter td.nowraponall, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre_filter td.nowraponall {
  white-space: normal;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre_filter td.nowraponall > *, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre_filter td.nowraponall > * {
  margin-bottom: 0.25em;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre_filter td.parentonrightofpage, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_titre_filter td.parentonrightofpage {
  direction: ltr !important;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre_filter > *:last-of-type, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre_filter > *:first-of-type, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre > *:last-of-type, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre > *:first-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre_filter > *:last-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre_filter > *:first-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre > *:last-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *.liste_titre > *:first-of-type {
  background: var(--colorbacktitle1) !important;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(odd) > *:last-of-type, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(odd) > *:first-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(odd) > *:last-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(odd) > *:first-of-type {
  background: var(--colorbacklinepair1);
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(even) > *:last-of-type, #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(even) > *:first-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(even) > *:last-of-type, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > *:nth-child(even) > *:first-of-type {
  background: var(--colorbacklineimpair1);
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive tr.highlight > td, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive tr.highlight > td {
  background: var(--colorbacklinepairchecked) !important;
}
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive tr.highlight:hover > td, #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive tr.highlight:hover > td {
  background: var(--colorbacklinepairhover) !important;
}
#id-right > .fiche > form#searchFormList[action*="blockedlog_list.php"] {
  height: initial !important;
  flex-grow: initial !important;
}
#id-right > .fiche > .tabBar > form[action="/admin/mails_senderprofile_list.php"], #id-right > .fiche > .tabBar > form[action="/admin/emailcollector_list.php"] {
  height: 100%;
}
#id-right > .fiche > form[action*="movement_list.php"] {
  position: static;
}
#id-right > .fiche div.liste_titre.liste_titre_bydiv.centpercent {
  min-height: 45px !important;
  flex-shrink: 0;
}

div.div-table-responsive {
  overflow: auto !important;
}

tr.liste_titre th.liste_titre_sel:not(.maxwidthsearch),
tr.liste_titre td.liste_titre_sel:not(.maxwidthsearch),
tr.liste_titre th.liste_titre:not(.maxwidthsearch),
tr.liste_titre td.liste_titre:not(.maxwidthsearch) {
  opacity: initial !important;
}

/* Bug sur mobile -> seulement sur la liste des interventions */
@media (hover: none) and (pointer: coarse) {
  table.liste tbody.ui-selectable {
    -ms-touch-action: unset !important;
    touch-action: unset !important;
  }
}
#ui-datepicker-div {
  z-index: 1010 !important;
}

@media (max-width: 768px) {
  div.liste_titre_bydiv .divsearchfield {
    font-size: 0.8rem;
    padding-bottom: 10px;
  }
  div.liste_titre_bydiv .divsearchfield li.select2-selection__choice {
    display: flex;
  }
  form#searchFormList > div.liste_titre.liste_titre_bydiv {
    display: inline-table !important;
  }
}
/**
 * Global z-index layout of the listing
 * See issue #89 for a visual representation
 */
form[action*="list.php"] > div.div-table-responsive > table {
  /**
   * Main Layout
   */
}
@media (min-width: 768px) {
  form[action*="list.php"] > div.div-table-responsive > table > * > tr > td:first-child, form[action*="list.php"] > div.div-table-responsive > table > * > tr > th:first-child {
    position: sticky;
    left: 0;
  }
}
form[action*="list.php"] > div.div-table-responsive > table > * > tr > td:first-child, form[action*="list.php"] > div.div-table-responsive > table > * > tr > th:first-child {
  z-index: 1;
}
form[action*="list.php"] > div.div-table-responsive > table > * > tr > td:last-child, form[action*="list.php"] > div.div-table-responsive > table > * > tr > th:last-child {
  position: sticky;
  right: 0;
  z-index: 1;
  border-left: var(--border-width) var(--border-style) var(--border-color-light);
}
form[action*="list.php"] > div.div-table-responsive > table {
  /**
   * Headers
   */
}
form[action*="list.php"] > div.div-table-responsive > table thead {
  position: sticky;
  top: 0;
  z-index: 3;
}
@media (min-width: 768px) {
  form[action*="list.php"] > div.div-table-responsive > table thead > tr > .liste_titre:first-child, form[action*="list.php"] > div.div-table-responsive > table thead > tr > .liste_titre_sel:first-child {
    position: sticky;
    left: 0;
    background: inherit;
  }
}
form[action*="list.php"] > div.div-table-responsive > table thead > tr > .liste_titre:first-child, form[action*="list.php"] > div.div-table-responsive > table thead > tr > .liste_titre_sel:first-child {
  z-index: 4;
}
form[action*="list.php"] > div.div-table-responsive > table thead > tr > .liste_titre:last-child {
  position: sticky;
  z-index: 4;
  right: 0;
  background: inherit;
}
form[action*="list.php"] > div.div-table-responsive > table thead > tr:first-child {
  position: sticky;
  z-index: 5;
  top: 0;
}
@media (min-width: 768px) {
  form[action*="list.php"] > div.div-table-responsive > table thead > tr:first-child > .liste_titre:first-child, form[action*="list.php"] > div.div-table-responsive > table thead > tr:first-child > .liste_titre_sel:first-child {
    position: sticky;
    left: 0;
    background: inherit;
  }
}
form[action*="list.php"] > div.div-table-responsive > table thead > tr:first-child > .liste_titre:first-child, form[action*="list.php"] > div.div-table-responsive > table thead > tr:first-child > .liste_titre_sel:first-child {
  z-index: 6;
}
form[action*="list.php"] > div.div-table-responsive > table thead > tr:first-child > .liste_titre:last-child {
  position: sticky;
  z-index: 6;
  right: 0;
  background: inherit;
}
form[action*="list.php"] > div.div-table-responsive > table {
  /**
   * Footer / Total line
   */
}
form[action*="list.php"] > div.div-table-responsive > table tr.liste_total {
  position: sticky;
  bottom: 0;
  z-index: 2 !important;
}
@media (min-width: 768px) {
  form[action*="list.php"] > div.div-table-responsive > table tr.liste_total > td:first-child {
    position: sticky;
    left: 0;
  }
}
form[action*="list.php"] > div.div-table-responsive > table tr.liste_total > td:first-child {
  z-index: 3;
}
form[action*="list.php"] > div.div-table-responsive > table tr.liste_total > td:last-child {
  position: sticky;
  z-index: 3;
  right: 0;
  border-left: var(--border-width) var(--border-style) var(--border-color-light);
}

.side-nav {
  width: 100%;
  max-width: var(--width-secondary-menu);
  height: 100%;
  padding: 0;
  border-right: var(--border-width) var(--border-style) var(--border-color-light) !important;
  overflow-y: auto;
  position: fixed;
  z-index: var(--z_index80);
  display: block !important;
  box-shadow: none;
  background: var(--colorbackvmenu1);
}
.side-nav > * {
  width: 90%;
  margin: 0 auto;
}
.side-nav #id-left {
  padding-top: 2rem;
  padding-bottom: 70px;
}
.side-nav #id-left .vmenu {
  width: 100%;
}
.side-nav #id-left .vmenu > .blockvmenu {
  display: flex;
  flex-direction: column;
  gap: 0.25em;
}
.side-nav #id-left .vmenu > .blockvmenu br {
  display: none !important;
}
.side-nav #id-left .vmenu > .blockvmenu > * {
  width: 100%;
}

#id-container > div.side-nav {
  display: flex !important;
}
#id-container > div.side-nav.open:not(.is-mobile) {
  width: 100%;
}
#id-container > div.side-nav.open:not(.is-mobile) #hidespan {
  padding-top: 2.5rem;
}
#id-container > div.side-nav.open:not(.is-mobile) .vmenu {
  display: block;
}
#id-container > div.side-nav.close:not(.is-mobile) {
  overflow-x: hidden;
  width: calc(2.5em + 20px);
}
#id-container > div.side-nav.close:not(.is-mobile) #hidespan {
  padding-top: 0px;
  padding-bottom: 2em;
  margin-left: 6px;
  border-bottom: 1px solid #eaeaea;
}
#id-container > div.side-nav.close:not(.is-mobile) #blockvmenusearch, #id-container > div.side-nav.close:not(.is-mobile) > .blockvmenu > div:not(.menu_titre), #id-container > div.side-nav.close:not(.is-mobile) .menu_contenu {
  display: none;
}
#id-container > div.side-nav.close:not(.is-mobile) span.hidden-menu-text {
  display: none;
}

form[action*="ihm.php"] > .center {
  position: sticky;
  bottom: 0;
}
form[action*="ihm.php"] > .center:not(:last-of-type) {
  display: none;
}

form[action="/git-dolibarr/htdocs/admin/modules.php"] {
  display: flex;
  flex-direction: column;
  gap: var(--row-gap);
}
form[action="/git-dolibarr/htdocs/admin/modules.php"] > * {
  width: var(--aligncenter);
  margin: 0 auto;
}
form[action="/git-dolibarr/htdocs/admin/modules.php"] .info {
  order: -1;
}
.dashboard-section {
  border: var(--border-width) var(--border-style);
  border-radius: var(--border-radius--medium) !important;
  background: var(--colorbackbody) !important;
  margin: 1em auto !important;
}
@media (min-width: 768px) {
  .dashboard-section {
    columns: 2;
  }
}
.dashboard-section * > .box table {
  border: var(--border-width) var(--border-style) var(--border-color-light);
  border-radius: var(--border-radius--medium);
}
.dashboard-section * > .box table tr.oddeven > td > a {
  text-decoration: underline;
  font-weight: bold;
}
.dashboard-section * > .box table tr:last-child > td {
  border: none !important;
}
.dashboard-section * > .box .box_titre > td {
  font-weight: bold;
}
.dashboard-section .title {
  column-span: all;
}
.dashboard-section .fichecenterbis {
  margin-top: 0px;
}
.dashboard-section .fichehalfleft {
  width: 100%;
}

div.div-table-responsive-no-min,
table.boxtable {
  border: var(--border-width) var(--border-style) var(--border-color-light) !important;
  border-radius: var(--border-radius--medium) !important;
  background: var(--colorbackbody) !important;
}
div.div-table-responsive-no-min > table,
table.boxtable > table {
  border-top: none;
}
div.div-table-responsive-no-min > table tr:last-child > *,
table.boxtable > table tr:last-child > * {
  border-bottom: none !important;
}

.fichecenter #boxhalfleft form[method=post] {
  border: var(--border-width) var(--border-style) var(--border-color-light) !important;
  border-radius: var(--border-radius--medium) !important;
  background: var(--colorbackbody) !important;
}
.fichecenter #boxhalfleft form[method=post] > table {
  border-top: none;
}
.fichecenter #boxhalfleft form[method=post] > table tr:last-child > * {
  border-bottom: none !important;
}

@media (max-width: 768px) {
  div.tabsAction {
    --position: relative;
  }
}
div.tabsAction {
  --margin: auto 0 0;
  /* Position */
  order: 99;
  flex: none;
  position: var(--position, sticky);
  bottom: 0;
  right: 0;
  left: 0;
  margin: var(--margin) !important;
  margin-top: auto !important;
  width: var(--width, 100%) !important; /*OVERLOAD --aligncenter */
  /* Inset layout */
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  white-space: nowrap;
  overflow-x: auto;
  padding: 1rem;
  /* Styles */
  border-top: unset;
  background: linear-gradient(transparent, var(--colorbackbody));
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  /*Disable custom content*/
  z-index: var(--z_index30);
}

.fiche > form > .center,
.tabBar > form > .center:last-of-type,
.tabBar > .center {
  --margin: auto -1rem 0 !important;
  --width: calc(100% + 2rem);
}
@media (max-width: 768px) {
  .fiche > form > .center,
  .tabBar > form > .center:last-of-type,
  .tabBar > .center {
    --position: relative;
  }
}
.fiche > form > .center,
.tabBar > form > .center:last-of-type,
.tabBar > .center {
  --margin: auto 0 0;
  /* Position */
  order: 99;
  flex: none;
  position: var(--position, sticky);
  bottom: 0;
  right: 0;
  left: 0;
  margin: var(--margin) !important;
  margin-top: auto !important;
  width: var(--width, 100%) !important; /*OVERLOAD --aligncenter */
  /* Inset layout */
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  white-space: nowrap;
  overflow-x: auto;
  padding: 1rem;
  /* Styles */
  border-top: unset;
  background: linear-gradient(transparent, var(--colorbackbody));
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  /*Disable custom content*/
}

div.tabsAction {
  z-index: var(--z_index70) !important;
}

div.liste_titre_bydiv {
  margin: 0;
}

img.pictotitle {
  max-width: 2em;
  max-height: 2em;
}

/* CUSTOM ITEM TIER GLOBAL */
a[href*="/societe/card.php"].classfortooltip {
  position: relative;
  padding-left: 1.25em;
  padding-right: 0.25em;
  white-space: normal;
}
a[href*="/societe/card.php"].classfortooltip .fa-building {
  top: 0;
  line-height: inherit;
  position: absolute;
  left: 0;
}

td a[href*="/societe/card.php"].classfortooltip {
  min-width: 200px;
  display: block;
}

/* CUSTOM OBJECT INTER */
a[href*="/interventionplus/interventionplus_card.php"].classfortooltip {
  white-space: nowrap;
}

.select2-dropdown.select2-dropdown--below {
  z-index: 1070;
}

ul.ulselectedfields {
  z-index: var(--z_index100);
}

@media (max-width: 768px) {
  .swal-makeorder {
    overflow-y: auto;
  }
}

.multichoicedoc {
  left: 240px !important;
  top: -20px;
}

div.info {
  color: var(--btn-color-utility);
  background: color-mix(in srgb, currentColor 16%, transparent);
}

#hidespan {
  padding: 0.2em;
  width: 16px;
  height: 16px;
  cursor: pointer;
}

#searchFieldsContainer > td.liste_titre.maxwidthsearch > div.nowrap > button > span, #searchFieldsContainer > td.liste_titre.maxwidthsearch > div.nowrap > div > button > span {
  color: black !important;
}

div.multiselectlinkto > ul.ulselectedfields.open {
  top: -100px !important;
}

div.multiselectlinkto > ul.ulselectedfields.open {
  max-height: 225px;
}

form#searchFormList table.liste,
form#searchFormList table.noborder,
form#searchFormList table.formdoc,
form#searchFormList div.noborder {
  margin-bottom: 60px !important;
}

div.tabs:first-of-type,
.fiche > div.tabs {
  position: sticky;
}
@media (max-width: 1024px) {
  div.tabs:first-of-type,
  .fiche > div.tabs {
    top: -15px !important;
  }
}
div.tabs:first-of-type,
.fiche > div.tabs {
  top: 0px;
  border-bottom: solid 1px var(--border-color-light) !important;
  margin: 0 auto 1rem !important;
  background: var(--colorbackbody);
  height: auto;
  z-index: var(--z_index50);
}
div.tabs:first-of-type:first-of-type,
.fiche > div.tabs:first-of-type {
  width: 100% !important;
}
div.tabs:first-of-type .badge,
.fiche > div.tabs .badge {
  background-color: var(--colorbackhmenu1);
}

div.popuptabset {
  background: var(--colorbackvmenu1) !important;
}

div.tabs[data-role*=controlgroup] {
  height: auto;
}

/* 
... Onglets masqués en mobile [...PLUS...] -> Spécifique sur mobile
Ce n'est pas un media screen css -> le regroupement des onglets peut-être via un code JS qui detecte la source ... LOL

Le code suivant surcharge le conteneur généré en mobile.
*/
div.fiche > table.table-fiche-title:first-of-type div,
div.titre,
div.quicklist-filter-list.centpercent {
  line-height: 1.125em !important;
  font-size: 1.125rem;
  font-weight: bold;
}

ul.tmenu:after {
  align-self: stretch;
  justify-self: stretch;
  z-index: var(--z_index30);
  background: linear-gradient(to right, transparent, var(--colorbackhmenu1));
  order: 5;
  position: sticky;
  pointer-events: none;
  bottom: 0;
  right: 0;
  min-width: 4em;
}

ul.tmenu {
  position: relative;
  width: 100%;
  display: inline-flex;
  flex-wrap: nowrap;
  align-items: center;
  align-content: stretch;
  gap: 0.1rem;
  color: var(--colortextbackhmenu);
}
ul.tmenu > li {
  display: var(--display, flex);
  align-items: var(--alignItems, center);
  justify-content: var(--justifyContent, center);
  align-self: stretch;
  padding: 0;
  min-width: var(--minWidth, auto) !important;
  flex: var(--flex, 1);
  /* ALTERNATIVES */
  order: 1;
}
ul.tmenu > li#mainmenutd_menu {
  z-index: var(--z_index70);
  left: 0;
  bottom: 0;
  right: 0;
  top: 0;
  position: sticky;
  background: linear-gradient(to right, transparent, var(--colorbackhmenu1));
}
ul.tmenu > li#mainmenutd_menu:before {
  content: "";
  background: var(--colortexttitlelink);
  display: block;
  position: absolute;
  top: 0.5em;
  aspect-ratio: 1;
  bottom: 0.5em;
  border-radius: 2em;
}
ul.tmenu > li#mainmenutd_menu a#mainmenua_menu {
  display: none !important;
}
ul.tmenu > li.tmenusel {
  z-index: var(--z_index40);
}
ul.tmenu > li.tmenusel:before {
  content: "";
  position: absolute;
  top: 0;
  opacity: 0.5;
  right: 0;
  bottom: 0;
  left: 0;
  background: var(--colorbackhmenu1);
  filter: invert(0.5);
  z-index: var(--z_index10);
}
ul.tmenu > li.tmenusel div.tmenucenter {
  position: relative;
  z-index: var(--z_index20);
}
ul.tmenu > li.tmenucompanylogo {
  order: -10;
}
ul.tmenu > li {
  /* CHILDRENS */
}
ul.tmenu > li:after {
  content: "";
  display: none;
}
ul.tmenu > li div.mainmenu {
  height: auto !important;
}
ul.tmenu > li:not(.tmenucompanylogo, #mainmenutd_menu) div.tmenucenter {
  position: relative;
  z-index: var(--z_index30);
}
ul.tmenu > li:not(.tmenucompanylogo, #mainmenutd_menu) div.tmenucenter:before {
  pointer-events: none;
  z-index: var(--z_index20);
  content: "";
  background: var(--colortexttitlelink);
  position: absolute;
  top: 50%;
  left: 50%;
  bottom: 50%;
  right: 50%;
  opacity: 0.2;
  border-radius: 100%;
  transition: all 0.5s;
}
ul.tmenu > li:not(.tmenucompanylogo, #mainmenutd_menu) div.tmenucenter:hover {
  opacity: 1;
}
ul.tmenu > li:not(.tmenucompanylogo, #mainmenutd_menu) div.tmenucenter:hover:before {
  pointer-events: none;
  opacity: 0.3;
  border-radius: 0%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.15s;
}
ul.tmenu > li:not(.tmenucompanylogo, #mainmenutd_menu) div.tmenucenter:active:before {
  background: var(--color_text_link);
  opacity: 0.6;
}
ul.tmenu > li:not(.tmenucompanylogo, #mainmenutd_menu) div.tmenucenter input:hover {
  opacity: 0.8;
}
ul.tmenu > li div.tmenucenter {
  display: flex;
  align-self: var(--alignSelf, stretch);
  flex-direction: var(--flexDirection, column);
  align-items: var(--alignItems, center);
  justify-content: var(--justifyContent, center);
  padding: 0 !important;
  height: var(--height, auto) !important;
  width: 100% !important;
  max-width: none;
}

#topmenu-login-dropdown > a {
  flex-direction: column;
  display: flex;
  justify-content: center;
  align-items: center;
}
#topmenu-login-dropdown > a:after {
  content: unset;
}
#topmenu-login-dropdown span.atoploginusername {
  display: none;
  font-size: 0.875rem;
  padding: 0.25rem;
  overflow: hidden;
  text-overflow: ellipsis;
}

span.fa.fa-print.atoplogin:before {
  content: "\f002";
}

body#mainbody input.button:not(.buttongen):not(.bordertransp),
body#mainbody a.button:not(.buttongen):not(.bordertransp),
body#mainbody *[class*=butAction],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) {
  font-size: 1rem;
  padding: 0.6em 1.2em;
  line-height: 1.1667em;
  color: var(--btn-color, var(--colortextlink)) !important;
  background: var(--btn-background, var(--colorbackbody)) !important;
  border: solid var(--border-width) var(--btn-background, currentColor);
  border-radius: var(--border-radius, 5px);
  text-transform: uppercase;
  font-weight: bold;
  cursor: pointer;
  white-space: nowrap;
  display: inline-flex;
  gap: 0.5em;
  position: relative;
  box-shadow: none;
  margin: 0.25em 0em !important;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp).h2g2multiselect__chevron,
body#mainbody a.button:not(.buttongen):not(.bordertransp).h2g2multiselect__chevron,
body#mainbody *[class*=butAction].h2g2multiselect__chevron,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]).h2g2multiselect__chevron {
  border-radius: 0 var(--border-radius, 5px) var(--border-radius, 5px) 0;
  border-left: 0;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp).h2g2multiselect__button,
body#mainbody a.button:not(.buttongen):not(.bordertransp).h2g2multiselect__button,
body#mainbody *[class*=butAction].h2g2multiselect__button,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]).h2g2multiselect__button {
  border-radius: var(--border-radius, 5px) 0 0 var(--border-radius, 5px);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp) i:not(.isfavorite),
body#mainbody input.button:not(.buttongen):not(.bordertransp) span,
body#mainbody a.button:not(.buttongen):not(.bordertransp) i:not(.isfavorite),
body#mainbody a.button:not(.buttongen):not(.bordertransp) span,
body#mainbody *[class*=butAction] i:not(.isfavorite),
body#mainbody *[class*=butAction] span,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) i:not(.isfavorite),
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) span {
  color: var(--btn-color, var(--colortextlink));
  margin: 0;
  align-content: center;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp),
body#mainbody a.button:not(.buttongen):not(.bordertransp),
body#mainbody *[class*=butAction],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) {
  position: relative;
  z-index: var(--z_index30);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp):before,
body#mainbody a.button:not(.buttongen):not(.bordertransp):before,
body#mainbody *[class*=butAction]:before,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]):before {
  pointer-events: none;
  z-index: var(--z_index20);
  content: "";
  background: currentColor;
  position: absolute;
  top: 50%;
  left: 50%;
  bottom: 50%;
  right: 50%;
  opacity: 0.2;
  border-radius: 100%;
  transition: all 0.5s;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp):hover,
body#mainbody a.button:not(.buttongen):not(.bordertransp):hover,
body#mainbody *[class*=butAction]:hover,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]):hover {
  opacity: 1;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp):hover:before,
body#mainbody a.button:not(.buttongen):not(.bordertransp):hover:before,
body#mainbody *[class*=butAction]:hover:before,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]):hover:before {
  pointer-events: none;
  opacity: 0.3;
  border-radius: 0%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.15s;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp):active:before,
body#mainbody a.button:not(.buttongen):not(.bordertransp):active:before,
body#mainbody *[class*=butAction]:active:before,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]):active:before {
  background: var(--color_text_link);
  opacity: 0.6;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp) input:hover,
body#mainbody a.button:not(.buttongen):not(.bordertransp) input:hover,
body#mainbody *[class*=butAction] input:hover,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) input:hover {
  opacity: 0.8;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=create"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=valid"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=print"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=received"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=accept_intervention"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=create"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=valid"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=print"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=received"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=accept_intervention"],
body#mainbody *[class*=butAction][href*="action=create"],
body#mainbody *[class*=butAction][href*="action=valid"],
body#mainbody *[class*=butAction][href*="action=print"],
body#mainbody *[class*=butAction][href*="action=received"],
body#mainbody *[class*=butAction][href*="action=accept_intervention"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=create"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=valid"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=print"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=received"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=accept_intervention"] {
  --btn-color: var(--btn-color-success);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[data-back-url*="action=set_waiting_part"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[data-back-url*="action=set_repaired"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[data-back-url*="action=set_waiting_part"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[data-back-url*="action=set_repaired"],
body#mainbody *[class*=butAction][data-back-url*="action=set_waiting_part"],
body#mainbody *[class*=butAction][data-back-url*="action=set_repaired"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[data-back-url*="action=set_waiting_part"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[data-back-url*="action=set_repaired"] {
  --btn-color: var(--btn-color-success);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[data-toggle*=dropdown],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[data-toggle*=dropdown],
body#mainbody *[class*=butAction][data-toggle*=dropdown],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[data-toggle*=dropdown] {
  --btn-color: var(--btn-color-success);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[name*=save], body#mainbody input.button:not(.buttongen):not(.bordertransp)[name*=sendit], body#mainbody input.button:not(.buttongen):not(.bordertransp)[name*=print], body#mainbody input.button:not(.buttongen):not(.bordertransp)[name*=linkit],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[name*=save],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[name*=sendit],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[name*=print],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[name*=linkit],
body#mainbody *[class*=butAction][name*=save],
body#mainbody *[class*=butAction][name*=sendit],
body#mainbody *[class*=butAction][name*=print],
body#mainbody *[class*=butAction][name*=linkit],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[name*=save],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[name*=sendit],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[name*=print],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[name*=linkit] {
  --btn-color: var(--btn-color-success);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[value*=Filtrer], body#mainbody input.button:not(.buttongen):not(.bordertransp)[value*=Ajouter],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[value*=Filtrer],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[value*=Ajouter],
body#mainbody *[class*=butAction][value*=Filtrer],
body#mainbody *[class*=butAction][value*=Ajouter],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[value*=Filtrer],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[value*=Ajouter] {
  --btn-color: var(--btn-color-success);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp).btnTakePicture, body#mainbody input.button:not(.buttongen):not(.bordertransp)#add_title_line, body#mainbody input.button:not(.buttongen):not(.bordertransp)#add_total_line, body#mainbody input.button:not(.buttongen):not(.bordertransp)#add_free_text, body#mainbody input.button:not(.buttongen):not(.bordertransp)#addpart, body#mainbody input.button:not(.buttongen):not(.bordertransp)#saveReceipt, body#mainbody input.button:not(.buttongen):not(.bordertransp)#outgoingcall,
body#mainbody input.button:not(.buttongen):not(.bordertransp) .goodActionButton,
body#mainbody a.button:not(.buttongen):not(.bordertransp).btnTakePicture,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#add_title_line,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#add_total_line,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#add_free_text,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#addpart,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#saveReceipt,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#outgoingcall,
body#mainbody a.button:not(.buttongen):not(.bordertransp) .goodActionButton,
body#mainbody *[class*=butAction].btnTakePicture,
body#mainbody *[class*=butAction]#add_title_line,
body#mainbody *[class*=butAction]#add_total_line,
body#mainbody *[class*=butAction]#add_free_text,
body#mainbody *[class*=butAction]#addpart,
body#mainbody *[class*=butAction]#saveReceipt,
body#mainbody *[class*=butAction]#outgoingcall,
body#mainbody *[class*=butAction] .goodActionButton,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]).btnTakePicture,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#add_title_line,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#add_total_line,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#add_free_text,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#addpart,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#saveReceipt,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#outgoingcall,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) .goodActionButton {
  --btn-color: var(--btn-color-success);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=modif"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=edit"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=export"], body#mainbody input.button:not(.buttongen):not(.bordertransp)#signalerror,
body#mainbody input.button:not(.buttongen):not(.bordertransp) .riskyActionButton,
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=modif"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=edit"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=export"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)#signalerror,
body#mainbody a.button:not(.buttongen):not(.bordertransp) .riskyActionButton,
body#mainbody *[class*=butAction][href*="action=modif"],
body#mainbody *[class*=butAction][href*="action=edit"],
body#mainbody *[class*=butAction][href*="action=export"],
body#mainbody *[class*=butAction]#signalerror,
body#mainbody *[class*=butAction] .riskyActionButton,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=modif"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=edit"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=export"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#signalerror,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) .riskyActionButton {
  --btn-color: var(--btn-color-warning);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=delete"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=close"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=remove"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=canceled"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=clone"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=merge"], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=setLocked&value=1"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=delete"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=close"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=remove"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=canceled"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=clone"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=merge"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=setLocked&value=1"],
body#mainbody *[class*=butAction][href*="action=delete"],
body#mainbody *[class*=butAction][href*="action=close"],
body#mainbody *[class*=butAction][href*="action=remove"],
body#mainbody *[class*=butAction][href*="action=canceled"],
body#mainbody *[class*=butAction][href*="action=clone"],
body#mainbody *[class*=butAction][href*="action=merge"],
body#mainbody *[class*=butAction][href*="action=setLocked&value=1"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=delete"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=close"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=remove"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=canceled"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=clone"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=merge"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=setLocked&value=1"] {
  --btn-color: var(--btn-color-danger);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[data-back-url*=refuse_intervention], body#mainbody input.button:not(.buttongen):not(.bordertransp)[data-back-url*="action=set_not_repairable"],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[data-back-url*=refuse_intervention],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[data-back-url*="action=set_not_repairable"],
body#mainbody *[class*=butAction][data-back-url*=refuse_intervention],
body#mainbody *[class*=butAction][data-back-url*="action=set_not_repairable"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[data-back-url*=refuse_intervention],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[data-back-url*="action=set_not_repairable"] {
  --btn-color: var(--btn-color-danger);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp).butActionDelete, body#mainbody input.button:not(.buttongen):not(.bordertransp)#action-clone, body#mainbody input.button:not(.buttongen):not(.bordertransp)[class*=butActionRefused],
body#mainbody input.button:not(.buttongen):not(.bordertransp) .dangerActionButton,
body#mainbody a.button:not(.buttongen):not(.bordertransp).butActionDelete,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#action-clone,
body#mainbody a.button:not(.buttongen):not(.bordertransp)[class*=butActionRefused],
body#mainbody a.button:not(.buttongen):not(.bordertransp) .dangerActionButton,
body#mainbody *[class*=butAction].butActionDelete,
body#mainbody *[class*=butAction]#action-clone,
body#mainbody *[class*=butAction][class*=butActionRefused],
body#mainbody *[class*=butAction] .dangerActionButton,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]).butActionDelete,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#action-clone,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[class*=butActionRefused],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) .dangerActionButton {
  --btn-color: var(--btn-color-danger);
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)[class*=butActionRefused],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[class*=butActionRefused],
body#mainbody *[class*=butAction][class*=butActionRefused],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[class*=butActionRefused] {
  opacity: 0.5;
}
body#mainbody input.button:not(.buttongen):not(.bordertransp)#scSendEmailBtn, body#mainbody input.button:not(.buttongen):not(.bordertransp)[id*=addCommentBtn], body#mainbody input.button:not(.buttongen):not(.bordertransp)[href*="action=presend"],
body#mainbody input.button:not(.buttongen):not(.bordertransp) .utilityActionButton,
body#mainbody a.button:not(.buttongen):not(.bordertransp)#scSendEmailBtn,
body#mainbody a.button:not(.buttongen):not(.bordertransp)[id*=addCommentBtn],
body#mainbody a.button:not(.buttongen):not(.bordertransp)[href*="action=presend"],
body#mainbody a.button:not(.buttongen):not(.bordertransp) .utilityActionButton,
body#mainbody *[class*=butAction]#scSendEmailBtn,
body#mainbody *[class*=butAction][id*=addCommentBtn],
body#mainbody *[class*=butAction][href*="action=presend"],
body#mainbody *[class*=butAction] .utilityActionButton,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])#scSendEmailBtn,
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[id*=addCommentBtn],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[href*="action=presend"],
body#mainbody a[class*=but]:not([class*=quicklist-button]):not([class*=cke]) .utilityActionButton {
  --btn-color: var(--btn-color-utility);
}
body#mainbody #linked_file > a {
  font-size: 1rem;
  padding: 0.6em 1.2em;
  line-height: 1.1667em;
  color: var(--btn-color, var(--colortextlink)) !important;
  background: var(--btn-background, var(--colorbackbody)) !important;
  border: solid var(--border-width) var(--btn-background, currentColor);
  border-radius: var(--border-radius, 5px);
  text-transform: uppercase;
  font-weight: bold;
  cursor: pointer;
  white-space: nowrap;
  display: inline-flex;
  gap: 0.5em;
  position: relative;
  box-shadow: none;
  margin: 0.25em 0em !important;
}
body#mainbody #linked_file > a.h2g2multiselect__chevron {
  border-radius: 0 var(--border-radius, 5px) var(--border-radius, 5px) 0;
  border-left: 0;
}
body#mainbody #linked_file > a.h2g2multiselect__button {
  border-radius: var(--border-radius, 5px) 0 0 var(--border-radius, 5px);
}
body#mainbody #linked_file > a i:not(.isfavorite),
body#mainbody #linked_file > a span {
  color: var(--btn-color, var(--colortextlink));
  margin: 0;
  align-content: center;
}
body#mainbody #linked_file > a {
  position: relative;
  z-index: var(--z_index30);
}
body#mainbody #linked_file > a:before {
  pointer-events: none;
  z-index: var(--z_index20);
  content: "";
  background: currentColor;
  position: absolute;
  top: 50%;
  left: 50%;
  bottom: 50%;
  right: 50%;
  opacity: 0.2;
  border-radius: 100%;
  transition: all 0.5s;
}
body#mainbody #linked_file > a:hover {
  opacity: 1;
}
body#mainbody #linked_file > a:hover:before {
  pointer-events: none;
  opacity: 0.3;
  border-radius: 0%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.15s;
}
body#mainbody #linked_file > a:active:before {
  background: var(--color_text_link);
  opacity: 0.6;
}
body#mainbody #linked_file > a input:hover {
  opacity: 0.8;
}
body#mainbody #linked_file > a {
  --btn-color: var(--btn-color-warning);
}

/* OVERLOAD DOLIBUTTON CONTAINER */
div.divButAction {
  order: 1;
  margin-bottom: initial !important;
}

div.tabsAction > * {
  order: 99 !important;
}

#my-booking-title, #my-booking-product-booking {
  order: 1 !important;
}

.dropdown.inline-block.dropdown-holder.open {
  display: flex;
  position: relative;
  z-index: var(--z_index100);
}
.dropdown.inline-block.dropdown-holder.open > .dropdown-content {
  bottom: 100%;
  background: linear-gradient(transparent, var(--colorbackbody));
  border: none;
  backdrop-filter: blur(3px);
  right: auto !important;
  display: flex;
  flex-direction: column;
}

body#mainbody div.dropdown-holder.open > a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[data-toggle*=dropdown] {
  background: var(--btn-color, var(--colortextlink)) !important;
  color: var(--btn-background, var(--colorbackbody)) !important;
}

body#mainbody div.dropdown-holder.open > a[class*=but]:not([class*=quicklist-button]):not([class*=cke])[data-toggle*=dropdown]::before {
  transform: none !important;
}

/* Surcharge of module Saturne on control code42 #401 */
.dropdown-toggle:after {
  display: inline-block !important;
}

div[class*=kanban] .col-s,
div[class*=kanban] .col-l,
div[class*=kanban] .col-xl {
  border: var(--border-width) var(--border-style);
  border-radius: var(--border-radius--medium) !important;
  background: var(--colorbackbody) !important;
}
div[class*=kanban] #kanban {
  border: var(--border-width) var(--border-style);
  border-radius: var(--border-radius--medium) !important;
  background: var(--colorbackbody) !important;
}

.logistic-box-container,
.logistic-inter-box {
  border: var(--border-width) var(--border-style);
  border-radius: var(--border-radius--medium) !important;
  background: var(--colorbackbody) !important;
}

form[action*="card.php"] > .tabBar,
form[action*="create_api_user.php"] > .tabBar {
  border: var(--border-width) var(--border-style);
  border-radius: var(--border-radius--medium) !important;
  background: var(--colorbackbody) !important;
  padding: 1em !important;
}

.jPicker.Container {
  z-index: var(--z_index100) !important;
  top: 50% !important;
  left: 50% !important;
  transform: translate(-50%, -50%);
  background: red !important;
}
.jPicker.Container:before {
  content: "";
  position: fixed;
  background: black;
  opacity: 0.5;
  width: 100vw;
  height: 100vh;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: -1;
}

/*@media screen and (min-width: 1250px) and (max-width: 1650px) {
  #tablelines > tbody > tr {
    td:not(.linecoldescription) {
      max-width: var(--cellwidth,200px) !important;
    }
  }

  #tablelines > thead > tr {
    td:not(.linecoldescription) {
      max-width: var(--cellwidth,200px) !important;
    }
  }
}

@media screen and (max-width: 1249px) {
  #tablelines > tbody > tr {
    td:not(.linecoldescription) {
      max-width: calc(var(--cellwidth,200px) - 50px) !important;
    }
  }

  #tablelines > thead > tr {
    td:not(.linecoldescription) {
      max-width: calc(var(--cellwidth,200px) - 50px) !important;
    }
  }
}

#tablelines > tbody > tr {
  td.linecoldescription > table > tbody > tr > td {
    min-width: 0px !important;
    max-width: 100% !important
  }

  td.linecoldescription {
    min-width: 0px !important;
    max-width: 100% !important
  }

  td:not(.linecoldescription) {
    min-width: 0px !important;
    width: fit-content !important;
  }
}

#tablelines > thead > tr {
  td:not(.linecoldescription) {
    min-width: 0px !important;
    width: fit-content !important;
  }
}

#tablelines > tbody > tr > td > select {
  min-width: 0px !important;
  max-width: 100% !important;
}
*/
table#tablelines > tbody > tr.drag.drop.oddeven.selected-sortable-item {
  background: color-mix(in srgb, var(--colorbackhmenu1) 50%, transparent) !important;
}

.timeline li .timeline-item {
  color: var(--colortext);
}
.timeline li .timeline-item .timeline-header {
  color: inherit;
}
.timeline li:nth-child(odd) .timeline-item {
  background: var(--colorbacklinepair1);
}
.timeline li:nth-child(even) .timeline-item {
  background: var(--colorbacklineimpair1);
}

:root {
  /* STRUCTURE */
  --aligncenter: calc(100% - 2rem);
  --column-gap: 1rem;
  --row-gap: 1rem;
  --width-secondary-menu: clamp(250px, 15vw, 300px);
  /* STYLES */
  /* – BORDER */
  --border-width: 1px;
  --border-style: solid;
  --border-color-light: color-mix(in srgb, currentColor 20%, transparent);
  /* – BORDER RADIUS */
  --border-radius--small: 0.25rem;
  --border-radius--medium: 0.5rem;
  --border-radius--large: 1rem;
  /* TODO */
  /* HISTORIC */
  --color_border_lines_h: solid 1px rgba(0, 0, 0, 0.15);
  --inner-lr: 2vw;
  --inner-tb: 0rem;
  /* TABLELINES */
  --cellwidth: 150px;
  /* V20 LISTINGS */
  --width-actioncolumn: 6em;
}

.dropdown-menu, .dropdown-menu > .bookmark-footer, .user-footer {
  background: var(--colorbackvmenu1) !important;
  color: var(--colortextbackhmenu) !important;
  /*.dropdown-item {
    color: var(--colorbackvmenu1) !important;
  }
  a.top-menu-dropdown-link {
    color: var(--colortextlink) !important;
  }
  .user-body {
    color: inherit !important;
  }
  .user-footer {
    background: var(--colorbackhmenu1) !important;
  }*/
}

.user-body {
  background: var(--colorbackvmenu1) !important;
}

@media (min-width: 768px) {
  .login_block .dropdown-menu, .login_block .dropdown-menu > .bookmark-footer {
    top: auto;
  }
}
.cal_other_month {
  background: var(--colorbackvmenu1) !important;
  color: var(--colortextvmenu1) !important;
}

.cal_current_month {
  background: var(--colorbackbody) !important;
  color: var(--colortext) !important;
}

.cal_today {
  background: var(--colorbackhmenu1) !important;
  color: var(--colortexthmenu1) !important;
}

.cal_today_peruser_impair {
  background: var(--colorbacklineimpair1) !important;
  color: var(--colortext) !important;
}

.cal_today_peruser_pair {
  background: var(--colorbacklinepair1) !important;
  color: var(--colortext) !important;
}

.ui-widget-content a {
  /* #278 - Overload color css inline by jquery-ui.css */
  color: currentColor;
}

@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
  div.ui-dialog.ui-corner-all.ui-widget.ui-widget-content.ui-front.ui-draggable.ui-resizable {
    top: 100px !important;
  }
  div.ui-dialog.ui-corner-all.ui-widget.ui-widget-content.ui-front.ui-draggable.ui-resizable > .ui-dialog-content.ui-widget-content {
    height: 100% !important;
    overflow-y: scroll;
  }
  div.ui-dialog.ui-corner-all.ui-widget.ui-widget-content.ui-front.ui-draggable.ui-resizable > .ui-dialog-content.ui-widget-content > object[name*=objectpreview] {
    width: 100% !important;
    margin-left: auto !important;
    margin-right: auto !important;
    display: block !important;
  }
}
body.FileArea {
  overflow: auto;
  position: relative;
}

html {
  font-size: 80%;
  /* 80% Of default navigator font size (16px * 0.8 = 12.8px) */
}
@media only screen and (-moz-min-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2), only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min-device-pixel-ratio: 2) {
  html {
    font-size: 90%;
  }
}

* {
  box-sizing: border-box;
}

body {
  font-size: 1rem !important;
  position: fixed;
  overflow: hidden;
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: column;
  padding: 0;
  margin: 0;
}
body #id-container {
  position: relative;
  max-width: 100%;
  overflow: hidden; /*hidden For custom scrollbar*/
  height: 100%;
  display: block;
}
body #id-right,
body #id-left {
  display: flex !important;
  flex-direction: column;
  padding-top: 0;
  padding-bottom: 0;
}
body #id-right {
  height: 100%;
  overflow: auto;
}
body #id-right > .fiche {
  margin: 0 !important;
  display: flex;
  flex-direction: column;
  position: relative;
  flex-grow: 1;
}
body #id-right > .fiche > *:not(form[action*="list.php"]) {
  height: auto;
  width: var(--aligncenter);
  margin-left: auto;
  margin-right: auto;
}
body #id-right > .fiche > *:not(form[action*="list.php"]):not(:first-child) {
  margin: var(--margin, 0 auto) !important;
}
body #id-right > .fiche > form[action*="card.php"]:not(:first-child), body #id-right > .fiche > form[action*="tasks.php"]:not(:first-child) {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
body #id-right > .fiche.admin-limits {
  display: block;
}

body:not(.sidebar-collapse) .side-nav {
  visibility: visible;
  position: absolute !important;
}
@media (max-width: 768px) {
  body:not(.sidebar-collapse) .side-nav {
    visibility: visible;
    position: absolute !important;
  }
}
body.sidebar-collapse .side-nav {
  visibility: hidden;
}
@media (max-width: 768px) {
  body.sidebar-collapse .side-nav {
    visibility: hidden;
  }
}
@media (min-width: 768px) {
  body.sidebar-collapse .side-nav {
    visibility: visible;
    position: absolute !important;
  }
  body.sidebar-collapse #id-right {
    width: calc(100% - var(--width-secondary-menu) - var(--width-primary-menu));
    overflow: auto;
    height: 100%;
    float: right;
    position: absolute;
    top: 0;
    right: 0;
  }
  body .menuhider {
    display: none !important;
  }
}

body:not(.sidebar-collapse) .mainmenu.menu.topmenuimage:before {
  content: "\f00d" !important;
}
body * > script,
body * > style,
body style,
body script {
  display: none !important;
}

.side-nav-vert div#tmenu_tooltip,
#id-top div#tmenu_tooltip {
  padding: 0;
  flex: 1;
  overflow-x: hidden;
  overflow-y: auto;
}

div.topmenuimage {
  left: auto !important;
  margin: auto !important;
  top: auto !important;
}
div.topmenuimage:before {
  font-size: 1em !important;
  line-height: 1;
  height: 2em;
}

div.login_block {
  display: flex !important;
  flex-direction: row;
  align-items: center;
  gap: 1rem;
  border-radius: 1rem;
  padding: 1rem 0.25rem;
  /**/
  position: relative;
  background: color-mix(in srgb, currentColor 10%, transparent);
  color: var(--colortextbackhmenu);
  width: auto !important;
  height: auto !important;
  min-width: initial;
  max-width: initial;
  line-height: inherit;
}
div.login_block div.login_block_other {
  margin: 0 auto;
  opacity: 0.5;
  flex-wrap: wrap;
  gap: 0.5rem;
}
div.login_block div.login_block_other * {
  color: inherit !important;
}
div.login_block div.login_block_other .login_block_elem {
  line-height: inherit;
  height: auto;
}
div.login_block span.aversion {
  font-size: 10px;
  font-family: monospace;
  text-overflow: ellipsis;
  overflow: hidden;
}
div.login_block span.aversion span {
  width: 1.9em;
  display: block;
  white-space: nowrap;
  overflow: hidden;
  font-size: inherit;
}
div.login_block span.aversion span:before {
  content: "V";
}
div.login_block > * {
  display: flex;
  flex-wrap: nowrap;
  align-items: center;
  justify-content: center;
}
div.login_block > * > .nowrap {
  flex-wrap: wrap;
}
div.login_block > .login_block_user {
  line-height: 1;
  height: auto;
  width: 100%;
}
div.login_block > .login_block_user .login_block_elem {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-direction: row;
  float: none;
  padding: 0 !important;
}
div.login_block > .login_block_user .login_block_elem > * {
  padding: 0 !important;
}
div.login_block > .login_block_user .login_block_elem a.login-dropdown-a:not([href*="user/card.php"]) {
  font-size: 1.3rem;
}
@media (max-width: 768px) {
  div.login_block > .login_block_user .user-header {
    border-top: var(--border-width) var(--border-style) var(--border-color-light);
  }
  div.login_block > .login_block_user .user-body > #topmenuloginmoreinfo {
    border-top: var(--border-width) var(--border-style) var(--border-color-light);
    height: 10em;
    overflow-y: auto;
  }
  div.login_block > .login_block_user .user-footer {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  div.login_block > .login_block_user .user-footer * {
    float: none !important;
  }
}
div.login_block .helppresentcircle {
  position: absolute;
  display: none;
}

/* DROPDOWN */
.dropdown-toggle {
  position: relative;
}

.tmenu div.dropdown-menu, .tmenu .dropdown-menu > div.bookmark-footer,
.login_block div.dropdown-menu,
.login_block .dropdown-menu > div.bookmark-footer,
.topnav div.dropdown-menu,
.topnav .dropdown-menu > div.bookmark-footer,
.side-nav-vert .user-menu .dropdown-menu,
.side-nav-vert .user-menu .dropdown-menu > .bookmark-footer {
  min-width: 100%;
  max-width: 400px;
  margin: 0;
  padding: 0;
  border: none;
}

.menuhider {
  display: inherit !important;
}

/* z-index 10 to 10 (max 100) */
:root {
  --z_index0: 0;
  --z_index10: 10;
  --z_index20: 20;
  --z_index30: 30;
  --z_index40: 40;
  --z_index50: 50;
  --z_index60: 60;
  --z_index70: 70;
  --z_index80: 80;
  --z_index90: 90;
  --z_index100: 100;
}

.side-nav-vert {
  color: var(--colortextbackhmenu);
  background: var(--colorbackhmenu1);
}

#mainmenuspan_h2g2,
#mainmenuspan_interventionplus,
#mainmenuspan_supercotrolia {
  aspect-ratio: 1/1;
  display: block;
  font-size: 1.25rem !important;
}

.supercotrolia {
  background: none !important;
}
.supercotrolia :before {
  font-family: "Material Icons" !important;
  font-weight: normal;
  font-style: normal;
  font-size: 1em; /* Preferred icon size */
  display: inline-block;
  line-height: 1em;
  text-transform: none;
  letter-spacing: normal;
  word-wrap: normal;
  white-space: nowrap;
  direction: ltr;
  vertical-align: middle;
  /* Support for all WebKit browsers. */
  -webkit-font-smoothing: antialiased;
  /* Support for Safari and Chrome. */
  text-rendering: optimizeLegibility;
  /* Support for Firefox. */
  -moz-osx-font-smoothing: grayscale;
  /* Support for IE. */
  font-feature-settings: "liga";
  content: "\e021";
}

div[id*=topmenu-] {
  line-height: 1em !important;
}

a.vsmenu[href*="action=create"] {
  color: var(--btn-color-success);
  font-weight: bold;
}

body.bodylogin {
  background: var(--colorbackbody);
}
body.bodylogin .login_center {
  color: var(--colortext);
  background: linear-gradient(color-mix(in srgb, var(--colorbackhmenu1) 80%, transparent), color-mix(in srgb, var(--colorbackbody) 20%, transparent)) !important;
}
body.bodylogin .login_center .login_table {
  background: var(--colorbackbody);
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit] {
  font-size: 1rem;
  padding: 0.6em 1.2em;
  line-height: 1.1667em;
  color: var(--btn-color, var(--colortextlink)) !important;
  background: var(--btn-background, var(--colorbackbody)) !important;
  border: solid var(--border-width) var(--btn-background, currentColor);
  border-radius: var(--border-radius, 5px);
  text-transform: uppercase;
  font-weight: bold;
  cursor: pointer;
  white-space: nowrap;
  display: inline-flex;
  gap: 0.5em;
  position: relative;
  box-shadow: none;
  margin: 0.25em 0em !important;
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit].h2g2multiselect__chevron {
  border-radius: 0 var(--border-radius, 5px) var(--border-radius, 5px) 0;
  border-left: 0;
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit].h2g2multiselect__button {
  border-radius: var(--border-radius, 5px) 0 0 var(--border-radius, 5px);
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit] i:not(.isfavorite),
body.bodylogin .login_center #login-submit-wrapper input[type=submit] span {
  color: var(--btn-color, var(--colortextlink));
  margin: 0;
  align-content: center;
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit] {
  position: relative;
  z-index: var(--z_index30);
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit]:before {
  pointer-events: none;
  z-index: var(--z_index20);
  content: "";
  background: currentColor;
  position: absolute;
  top: 50%;
  left: 50%;
  bottom: 50%;
  right: 50%;
  opacity: 0.2;
  border-radius: 100%;
  transition: all 0.5s;
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit]:hover {
  opacity: 1;
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit]:hover:before {
  pointer-events: none;
  opacity: 0.3;
  border-radius: 0%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.15s;
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit]:active:before {
  background: var(--color_text_link);
  opacity: 0.6;
}
body.bodylogin .login_center #login-submit-wrapper input[type=submit] input:hover {
  opacity: 0.8;
}
body.bodylogin .login_center .login_table_title {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  white-space: nowrap;
  color: var(--colortext) !important;
  opacity: 0.7;
  text-shadow: unset;
  padding: 0;
  margin: 0;
}

table.xdebug-error.xe-warning {
  margin-left: calc(var(--width-primary-menu) * 1.1);
}

/* Surcharge des modules / Développement spécifique modules */
/*InterventionPlus...*/
span#description {
  font-weight: bold;
}

span#ref_customer,
span#priority,
span#type,
span#failure_frequency,
span#warranty_return,
span#warranty_prev_inter,
span#ref,
span#manufacturer {
  font-weight: bold;
  display: inline;
  color: var(--colortextlink);
}

body.edit span#ref_customer,
body.edit span#priority,
body.edit span#type,
body.edit span#failure_frequency,
body.edit span#warranty_return,
body.edit span#warranty_prev_inter,
body.edit span#ref,
body.edit span#manufacturer {
  display: block;
}

/* WIP -> To integrate in super CTRL Stylesheet */
section.dashboard-section {
  overflow: inherit !important;
  column-gap: var(--column-gap, 1rem);
}
section.dashboard-section .badge.badge-status {
  display: block !important;
  float: inherit !important;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.25em;
  padding: 0.5em 0.25em;
}
section.dashboard-section .badge.badge-status .material-icons {
  font-size: 1.5em;
  margin-top: -0.2em;
  line-height: 1em;
  vertical-align: middle;
}

body div.fiche > .interventionplus-box-container {
  width: 100% !important;
  display: grid;
  grid-template-columns: repeat(var(--columns, 1), 1fr);
  grid-template-rows: auto;
  gap: var(--column-gap, 1.5rem);
}
@media (min-width: 1024px) {
  body div.fiche > .interventionplus-box-container {
    --columns: 2;
    grid-template-areas: "Intervention Customer" "Shipping Dossier" "Command Export" "Parts Devis" "Comment Cars" "Outsourcing .";
  }
}
@media (min-width: 1440px) {
  body div.fiche > .interventionplus-box-container {
    --columns: 3;
    /* !BELOW! SPECIFIC FOR SUPERCOTROLIA INTERVENTION PLUS  */
    grid-template-areas: "Intervention Customer Shipping" "Dossier Command Export" "Parts Devis Comment" "Cars Outsourcing .";
  }
}
@media (min-width: 1920px) {
  body div.fiche > .interventionplus-box-container {
    --columns: 4;
    grid-template-areas: "Intervention Customer Shipping Dossier" "Command Export Parts Devis" "Comment Cars Outsourcing .";
  }
}
body div.fiche > .interventionplus-box-container > * {
  /* Important pour safari */
  display: inline-block;
  width: 100%;
  /* Disable grid-row for grid-area -> BUG! */
}
body {
  /*

   ______ ____   _____ _    _  _____ 
  |  ____/ __ \ / ____| |  | |/ ____|
  | |__ | |  | | |    | |  | | (___  
  |  __|| |  | | |    | |  | |\___ \ 
  | |   | |__| | |____| |__| |____) |
  |_|    \____/ \_____|\____/|_____/ 

   */
}
/* !BELOW! SPECIFIC FOR SUPERCOTROLIA INTERVENTION PLUS  */
@media (min-width: 1024px) {
  .interventionplus-box-container .box[id=SuperCotroliaBoxOutsourcing] {
    grid-area: Outsourcing;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxCars] {
    grid-area: Cars;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxIntervention] {
    grid-area: Intervention;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxDossier] {
    grid-area: Dossier;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxParts] {
    grid-area: Parts;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxCustomer] {
    grid-area: Customer;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxCommand] {
    grid-area: Command;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxDevis] {
    grid-area: Devis;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxShipping] {
    grid-area: Shipping;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxExport] {
    grid-area: Export;
  }
  .interventionplus-box-container .box[id=SuperCotroliaBoxComment] {
    grid-area: Comment;
  }
}
.historic-header {
  width: 100%;
}
.historic-header .filter-container {
  display: flex;
  overflow-x: auto;
  justify-content: space-around;
}
.historic-header .filter-container > * {
  padding: 1rem;
  cursor: pointer;
  border: solid 1px;
}
.historic-header .filter-container > *:before {
  font-family: "Material Icons" !important;
  font-weight: normal;
  font-style: normal;
  font-size: 1em; /* Preferred icon size */
  display: inline-block;
  line-height: 1em;
  text-transform: none;
  letter-spacing: normal;
  word-wrap: normal;
  white-space: nowrap;
  direction: ltr;
  vertical-align: middle;
  /* Support for all WebKit browsers. */
  -webkit-font-smoothing: antialiased;
  /* Support for Safari and Chrome. */
  text-rendering: optimizeLegibility;
  /* Support for Firefox. */
  -moz-osx-font-smoothing: grayscale;
  /* Support for IE. */
  font-feature-settings: "liga";
  content: "\e836";
  margin-right: 0.5em;
}
.historic-header .filter-container > *.active {
  order: -1;
  font-weight: bold;
}
.historic-header .filter-container > *.active:before {
  content: "\e837";
}

ul#historic {
  width: 100%;
  display: flex;
  flex-direction: column-reverse;
  padding: 0;
  margin-bottom: 3rem;
  border: var(--color_border_lines_h);
  border-bottom: none;
  list-style: none;
}
ul#historic > li {
  padding: 1rem 2rem;
  border-top: var(--color_border_lines_h);
  background: var(--colorbacklinepair1);
}
ul#historic > li:last-child {
  border-bottom: var(--color_border_lines_h);
}
ul#historic > li:nth-child(even) {
  background: var(--colorbacklinepair1);
}
ul#historic > li .timeline-content {
  position: relative;
}
ul#historic > li .timeline-content .timeline-icon {
  left: -2rem;
  top: 50%;
  transform: translateX(-50%) translateY(-50%);
  background: white;
  position: absolute;
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  text-align: center;
  line-height: 2rem;
}
ul#historic > li .timeline-content .timeline-content-container {
  display: flex;
  align-items: center;
}
ul#historic > li .timeline-content .timeline-content-container > * {
  flex-grow: 1;
}
ul#historic > li .timeline-content .timeline-content-container > * > * {
  display: flex;
  align-items: center;
}
ul#historic > li .timeline-content .timeline-content-container > * > * > * {
  margin: 0 0.5rem 0 0;
  padding: 0;
}
ul#historic > li .timeline-content .timeline-content-container > * > * > *:first-child {
  white-space: nowrap;
  float: left;
}
ul#historic > li .timeline-content .timeline-content-container > * > * > *[onclick*=hideField] {
  white-space: nowrap;
}
ul#historic > li .timeline-content .timeline-content-container > * > * > *.timeline-msg {
  white-space: nowrap;
}
ul#historic > li .timeline-content .timeline-content-container > * > * > *.badge {
  white-space: nowrap;
  padding: 0.5rem;
}
@media (max-width: 768px) {
  ul#historic > li .timeline-content .timeline-content-container > * > * {
    display: block;
  }
}
ul#historic > li .timeline-content .timeline-content-container .timeline-date {
  flex-grow: 0;
  font-weight: bold;
}
ul#historic > li.id-statut {
  z-index: var(--z_index70);
  margin-top: 2rem;
}

.interventionplus-box {
  width: 100% !important;
  padding: 1rem var(--inner-lr);
}

div.fiche .interventionplus-box-container-footer {
  width: 100% !important;
  overflow-x: auto !important;
  position: sticky !important;
  bottom: 0 !important;
  margin: 0 !important;
}
div.fiche .interventionplus-box-container-header {
  position: sticky;
  top: 0;
  z-index: var(--z_index80);
  display: flex;
  justify-content: flex-start;
  align-items: center;
  margin-left: calc(var(--inner-lr) * -1);
  margin-right: calc(var(--inner-lr) * -1);
  width: auto;
  background: var(--colorbackbody);
  border-bottom: var(--color_border_lines_v);
  border-bottom: var(--border-width) var(--border-style) var(--border-color-light) !important;
  width: 100% !important;
  margin: 0 !important;
}
div.fiche .interventionplus-box-container-header > * {
  display: flex;
}
div.fiche .interventionplus-box-container-header > *.left-side {
  flex-grow: 1;
}
div.fiche .interventionplus-box-container-header > *.left-side .navigation {
  display: flex;
}
div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item a {
  gap: 0.5em;
  text-decoration: none;
  display: flex;
  align-items: center;
  align-content: stretch;
  margin: 0;
  padding: 1em;
  height: var(--height, 100%);
}
div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item a .badge {
  margin-top: 0;
  margin-bottom: 0;
  padding: 0.5em;
  line-height: 0.5em;
  vertical-align: middle;
  font-size: 0.8em;
  border: solid 1px;
  background: transparent;
  color: currentColor !important;
}
div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item.active, div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item.tabsElemActive {
  opacity: 1;
  color: var(--colortextlink);
}
div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item.active a, div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item.tabsElemActive a {
  box-shadow: inset 0 -4px currentColor;
}
div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item.active:after, div.fiche .interventionplus-box-container-header > *.left-side .navigation .navigation__item.tabsElemActive:after {
  opacity: 0.1;
}
div.fiche .interventionplus-box-container-header > *.right-side {
  padding: 0 0.8rem;
  margin: 0;
}
div.fiche .interventionplus-box-container-header > *.right-side a {
  gap: 0.5em;
  text-decoration: none;
  display: flex;
  align-items: center;
  align-content: stretch;
  margin: 0;
  padding: 1em;
  height: var(--height, 100%);
}
div.fiche .interventionplus-box-container-header > *.right-side a .badge {
  margin-top: 0;
  margin-bottom: 0;
  padding: 0.5em;
  line-height: 0.5em;
  vertical-align: middle;
  font-size: 0.8em;
  border: solid 1px;
  background: transparent;
  color: currentColor !important;
}
div.fiche .interventionplus-box-container-header > *.right-side i {
  padding-right: 1rem;
}
div.fiche .interventionplus-box {
  margin-left: 0 !important;
  margin-top: 0 !important;
}
div.fiche .interventionplus-box-container {
  margin-left: calc(var(--inner-lr) * -1);
  margin-right: calc(var(--inner-lr) * -1);
  padding: 1rem var(--inner-lr);
}
div.fiche .interventionplus-box-container > .fichecenter {
  display: block;
}

/*@include max-width-sm() {
  //#147
  .divsearchfield {
    display: flex;
    flex-wrap: wrap;
    width: 30%;
    font-size: 0.8rem;
    span {
      width: 100% !important;
    }
    b {
      left: 84% !important;
    }
  }
}*/
body #addPartBtn {
  display: none;
}
body.focus a[href*="focus=0"] {
  opacity: 1;
  color: var(--colortextlink);
}
body.focus a[href*="focus=0"] a {
  box-shadow: inset 0 -4px currentColor;
}
body.focus a[href*="focus=0"]:after {
  opacity: 0.1;
}
body.edit #addPartBtn {
  display: inherit;
}
body.edit a[href*="edit=0"] {
  opacity: 1;
  color: var(--colortextlink);
}
body.edit a[href*="edit=0"] a {
  box-shadow: inset 0 -4px currentColor;
}
body.edit a[href*="edit=0"]:after {
  opacity: 0.1;
}

body.focus .breadCrumbHolder.module {
  padding-left: 0;
}
body.focus #id-container {
  padding: 0;
  width: 100%;
  max-width: 100%;
  margin: 5;
}
body.focus #id-container #id-right {
  width: 100%;
}

#id-left #BoxInterventionSidebar * {
  line-height: initial !important;
}
#id-left #BoxInterventionSidebar > * {
  margin-bottom: 1em;
}
#id-left #BoxInterventionSidebar > *[class*=boxfield] {
  padding-left: 0;
  padding-right: 0;
}
#id-left #BoxInterventionSidebar > [class*=boxfield] {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
#id-left #BoxInterventionSidebar .link-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
#id-left #BoxInterventionSidebar .intervention-sidebar__elem {
  position: sticky;
  top: 0;
}
#id-left #BoxInterventionSidebar .intervention-sidebar__elem > * {
  margin: 0;
}
#id-left #BoxInterventionSidebar .intervention-sidebar__elem .label {
  font-size: 0.75rem;
  text-transform: uppercase;
  margin-bottom: 0.5rem;
  margin-top: 0.5rem;
  display: block;
  letter-spacing: 0.0667rem;
  opacity: 1;
}
#id-left #BoxInterventionSidebar .intervention-sidebar__elem span {
  line-height: 1.125em !important;
  font-size: 1.125rem;
  font-weight: bold;
}
#id-left #BoxInterventionSidebar .intervention-sidebar__elem:after {
  content: "";
  display: block;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  z-index: var(--z_index0);
  /**/
  background: var(--color_back_vmenu);
  -webkit-mask-image: linear-gradient(black, transparent);
}

.interventionplus-box-container .box {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  overflow: hidden;
  box-sizing: border-box;
  border-radius: var(--border-radius, 1rem);
  border: var(--border-width) var(--border-style) var(--border-color-light);
  background: var(--colorbacklinepair1);
  padding: 0;
  transition: all 0.5s ease-in-out;
}
.interventionplus-box-container .box .header {
  padding: 0.5rem 1rem;
  position: sticky;
  top: 0;
  z-index: var(--z_index60);
  width: 100%;
  transition: all 0.5s ease-out;
  color: var(--colortexttitle);
  background-color: var(--colorbacktitle1);
  border-bottom: var(--border-width) var(--border-style) var(--border-color-light);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.interventionplus-box-container .box .header h3 {
  font-size: 1rem;
  font-weight: bold;
  white-space: nowrap;
  display: flex;
  align-items: baseline;
  margin: 0;
}
.interventionplus-box-container .box .header h3 i {
  float: left;
  margin-right: 0.5rem;
}
.interventionplus-box-container .box .header ul {
  margin: 0;
  list-style: none;
  padding: 0;
  display: flex;
  align-items: center;
}
.interventionplus-box-container .box .header ul li {
  font-size: 1rem;
  padding: 0.6em 1.2em;
  line-height: 1.1667em;
  color: var(--btn-color, var(--colortextlink)) !important;
  background: var(--btn-background, var(--colorbackbody)) !important;
  border: solid var(--border-width) var(--btn-background, currentColor);
  border-radius: var(--border-radius, 5px);
  text-transform: uppercase;
  font-weight: bold;
  cursor: pointer;
  white-space: nowrap;
  display: inline-flex;
  gap: 0.5em;
  position: relative;
  box-shadow: none;
  margin: 0.25em 0em !important;
}
.interventionplus-box-container .box .header ul li.h2g2multiselect__chevron {
  border-radius: 0 var(--border-radius, 5px) var(--border-radius, 5px) 0;
  border-left: 0;
}
.interventionplus-box-container .box .header ul li.h2g2multiselect__button {
  border-radius: var(--border-radius, 5px) 0 0 var(--border-radius, 5px);
}
.interventionplus-box-container .box .header ul li i:not(.isfavorite),
.interventionplus-box-container .box .header ul li span {
  color: var(--btn-color, var(--colortextlink));
  margin: 0;
  align-content: center;
}
.interventionplus-box-container .box .header ul li {
  position: relative;
  z-index: var(--z_index30);
}
.interventionplus-box-container .box .header ul li:before {
  pointer-events: none;
  z-index: var(--z_index20);
  content: "";
  background: currentColor;
  position: absolute;
  top: 50%;
  left: 50%;
  bottom: 50%;
  right: 50%;
  opacity: 0.2;
  border-radius: 100%;
  transition: all 0.5s;
}
.interventionplus-box-container .box .header ul li:hover {
  opacity: 1;
}
.interventionplus-box-container .box .header ul li:hover:before {
  pointer-events: none;
  opacity: 0.3;
  border-radius: 0%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.15s;
}
.interventionplus-box-container .box .header ul li:active:before {
  background: var(--color_text_link);
  opacity: 0.6;
}
.interventionplus-box-container .box .header ul li input:hover {
  opacity: 0.8;
}
.interventionplus-box-container .box .header ul li {
  border: none;
  opacity: 0.6;
  font-size: 0.8rem;
  white-space: nowrap;
  background: transparent;
  cursor: pointer;
}
.interventionplus-box-container .box .header ul li span {
  display: none;
}
.interventionplus-box-container .box .header ul .reduce,
.interventionplus-box-container .box .header ul .unreduce {
  display: none;
}
.interventionplus-box-container .box > .content {
  padding: 1rem 1rem;
}
.interventionplus-box-container .box > .content * {
  break-inside: avoid !important;
}
.interventionplus-box-container .box .footer {
  height: 1px;
}
.interventionplus-box-container .box a.boxActionBtn {
  font-size: 1rem;
  padding: 0.6em 1.2em;
  line-height: 1.1667em;
  color: var(--btn-color, var(--colortextlink)) !important;
  background: var(--btn-background, var(--colorbackbody)) !important;
  border: solid var(--border-width) var(--btn-background, currentColor);
  border-radius: var(--border-radius, 5px);
  text-transform: uppercase;
  font-weight: bold;
  cursor: pointer;
  white-space: nowrap;
  display: inline-flex;
  gap: 0.5em;
  position: relative;
  box-shadow: none;
  margin: 0.25em 0em !important;
}
.interventionplus-box-container .box a.boxActionBtn.h2g2multiselect__chevron {
  border-radius: 0 var(--border-radius, 5px) var(--border-radius, 5px) 0;
  border-left: 0;
}
.interventionplus-box-container .box a.boxActionBtn.h2g2multiselect__button {
  border-radius: var(--border-radius, 5px) 0 0 var(--border-radius, 5px);
}
.interventionplus-box-container .box a.boxActionBtn i:not(.isfavorite),
.interventionplus-box-container .box a.boxActionBtn span {
  color: var(--btn-color, var(--colortextlink));
  margin: 0;
  align-content: center;
}
.interventionplus-box-container .box a.boxActionBtn {
  position: relative;
  z-index: var(--z_index30);
}
.interventionplus-box-container .box a.boxActionBtn:before {
  pointer-events: none;
  z-index: var(--z_index20);
  content: "";
  background: currentColor;
  position: absolute;
  top: 50%;
  left: 50%;
  bottom: 50%;
  right: 50%;
  opacity: 0.2;
  border-radius: 100%;
  transition: all 0.5s;
}
.interventionplus-box-container .box a.boxActionBtn:hover {
  opacity: 1;
}
.interventionplus-box-container .box a.boxActionBtn:hover:before {
  pointer-events: none;
  opacity: 0.3;
  border-radius: 0%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.15s;
}
.interventionplus-box-container .box a.boxActionBtn:active:before {
  background: var(--color_text_link);
  opacity: 0.6;
}
.interventionplus-box-container .box a.boxActionBtn input:hover {
  opacity: 0.8;
}
.interventionplus-box-container .box a.boxActionBtn {
  background: var(--color_text_title_link);
  color: var(--colorbackbody);
}
.interventionplus-box-container .box {
  /**/
}
.interventionplus-box-container .box header ul {
  position: relative;
}
.interventionplus-box-container .box header ul:before {
  padding-right: 0.5rem;
  font-size: 0.8rem;
  opacity: 0.5;
  pointer-events: none;
  text-transform: uppercase;
}

body #id-right > .fiche > #interventionplus-box-container-footer {
  z-index: var(--z_index70);
}

.box.grid-xs header ul:before {
  content: "xs";
}

.box.grid-s header ul:before {
  content: "s";
}

.box.grid-m header ul:before {
  content: "m";
}

.box.grid-l header ul:before {
  content: "l";
}

.box.grid-xl header ul:before {
  content: "xl";
}

.box.grid-xxl header ul:before {
  content: "xxl";
}

#BoxInterventionChat .boxchat {
  height: 256px;
}

@keyframes animate-stripes {
  0% {
    background-position: 0 0;
  }
  100% {
    background-position: 60px 0;
  }
}
#BoxInterventionStatus {
  grid-row: span 2;
}
#BoxInterventionStatus .content {
  height: 100%;
  padding-top: 0;
  padding-bottom: 0;
}
#BoxInterventionStatus .status-container {
  column-span: all !important;
  overflow-x: scroll;
  height: 100%;
  display: flex;
  align-items: center;
  z-index: var(--z_index30);
}
#BoxInterventionStatus .status-container .status-item {
  height: 2.6rem;
  flex-basis: 1%;
  margin: 0;
  padding: 0.25em 1em;
  border: none;
  position: relative;
  /**/
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  margin: 0.25em 0.5em;
  white-space: nowrap;
  border: solid 1px;
  color: initial !important;
  opacity: 0.5;
  border-radius: 5rem;
  text-align: right;
}
#BoxInterventionStatus .status-container .status-item span {
  line-height: 1.5rem;
  margin-right: 0.5rem;
}
#BoxInterventionStatus .status-container .status-item:before {
  z-index: var(--z_index30);
  position: absolute;
  content: "";
  display: none;
  width: 50%;
  top: 0.5rem;
  height: 1rem;
  opacity: 1;
  z-index: var(--z_index10);
}
#BoxInterventionStatus .status-container .status-item:before {
  left: 0;
}
#BoxInterventionStatus .status-container .status-item:first-child:before {
  display: none;
}
#BoxInterventionStatus .status-container .status-item {
  /* CUSTOM STATUS ON CHRONOLOGIE */
}
#BoxInterventionStatus .status-container .status-item.status-active {
  opacity: 1;
  border: solid 1px transparent;
  position: sticky;
  right: 0;
  align-items: center;
  font-weight: bold;
  color: var(--contrasted, currentColor) !important;
  background: var(--color, color-mix(in srgb, currentColor 20%, transparent)) !important;
}
#BoxInterventionStatus .status-container .status-item.status-active:before {
  animation: animate-stripes 2s linear infinite;
}
[class*=boxfield] {
  padding: 0.25rem 0 0.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
[class*=boxfield]:after {
  content: "";
  display: inline-block;
  height: 1px;
  border-top: dotted 1px currentColor;
  opacity: 0.5;
  flex-grow: 1;
  order: 2;
  margin: 0 0.5rem;
}
[class*=boxfield] > .label {
  margin-right: 0.25em;
  order: 1;
  font-size: 0.75rem;
  text-transform: uppercase;
  margin-bottom: 0.5rem;
  margin-top: 0.5rem;
  display: block;
  letter-spacing: 0.0667rem;
  opacity: 1;
}
[class*=boxfield] > .content {
  display: inline-block;
  text-align: right;
  order: 3;
}
[class*=boxfield] > .content > * {
  cursor: default;
  background: transparent;
}
[class*=boxfield] > .content > * {
  width: 100%;
  max-width: 100%;
  display: block;
}
[class*=boxfield] > .content .select2-container {
  padding: 0 !important;
}
[class*=boxfield] > .content {
  /* FIELDS TYPOLOGIES */
}
[class*=boxfield-textarea] > .content {
  text-align: left;
}

body.edit [class*=boxfield][class*=-editable] {
  padding: 0.5rem 0;
  flex-wrap: wrap;
  box-shadow: none;
}
body.edit [class*=boxfield][class*=-editable]:hover > .label {
  opacity: 1;
}
body.edit [class*=boxfield][class*=-editable] > .content {
  width: 100%;
  text-align: left;
}
body.edit [class*=boxfield][class*=-editable] > .content > * {
  cursor: default;
  padding: 0.6rem;
  font-size: 1rem;
  display: inline-block;
  font-family: inherit !important;
  outline: none;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  transition: all 0.25s;
  border: none;
  background: var(--background_field);
}
body.edit [class*=boxfield][class*=-editable] > .content > *:focus {
  background: var(--color_backline_checked) !important;
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  box-shadow: 0 6px 0 0 currentColor !important;
}
body.edit [class*=boxfield][class*=-editable] > .content > * {
  box-shadow: 0 0px 0 0 currentColor, inset 0 -1px currentColor;
}
body.edit [class*=boxfield][class*=-editable] > .content > *:hover {
  box-shadow: 0 3px 0 0 currentColor, inset 0 -3px currentColor;
  background: var(--color_backline_hover);
}
body.edit [class*=boxfield][class*=-editable] > .content > * {
  font-weight: normal;
  min-height: 2.4rem;
}
body.edit [class*=boxfield][class*=-editable] > .content[data-type=textarea] {
  overflow-y: hidden;
}
body.edit [class*=boxfield][class*=-editable] > .content[data-type=textarea] > * {
  margin: 0 !important;
  resize: none;
  width: 100% !important;
  height: 10.2em;
}
body.edit [class*=boxfield][class*=-editable] > .content[data-type=select] > * {
  position: relative;
}
body.edit [class*=boxfield][class*=-editable] > .content[data-type=select] > *:after {
  display: flex;
  align-items: center;
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  width: 1rem;
  content: "v";
}
body.edit [class*=boxfield][class*=-editable][class*=required] > .label {
  font-weight: bold;
  color: var(--color_text_title_link);
}
body.edit [class*=boxfield][class*=-editable][class*=required] > .label:before {
  color: currentColor;
  content: "*";
  font-size: 1.5em;
  line-height: 0em;
  margin-right: 0.2em;
}
.box-section .box-section-header {
  padding: 1rem 0 0;
  font-weight: bold;
}
.box-section .box-section-header .box-section-collapse-btn {
  display: none;
}
.box-section .box-section-collapsible {
  height: auto;
  max-height: 100%;
  padding: 0 0 1rem;
}
.box-section .box-section-collapsible.expanded {
  max-height: 100%;
}

.box-section > .boxfield-text {
  order: 1;
  grid-column: 1/-1;
}
.box-section > [class*=carousel] {
  order: 2;
}

body.edit [class*=boxfield-select] > .content[data-key=statut] > * {
  padding: 0;
}
body.edit [class*=boxfield-select] > .content[data-key=statut] > * .badge {
  margin: 0;
}

div.carousel {
  grid-column: 1/-1 !important;
  width: auto;
  max-width: 100%;
  height: auto;
  height: 25vh;
  min-height: 200px;
  max-height: 300px;
  position: relative;
  border-radius: var(--border-radius, 1rem);
  background: var(--colorbackbody);
  overflow: hidden;
  transition: all 0.5s;
}
div.carousel:hover {
  box-shadow: 0 0 3rem -1.5rem var(--colorbackhmenu1);
}
div.carousel .carousel__viewport {
  height: 100%;
}
div.carousel .carousel__viewport .carousel__track {
  height: 100%;
}
div.carousel .carousel__viewport .carousel__track .carousel__slide {
  height: 100%;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}
div.carousel .carousel__viewport .carousel__track .carousel__slide a {
  display: flex;
  width: auto;
  height: 100%;
  transform: scale(1);
  transition: all 1s;
  overflow: hidden;
  align-items: center;
}
div.carousel .carousel__viewport .carousel__track .carousel__slide a img {
  margin: 0 1rem !important;
  height: 100%;
  width: 100%;
  object-fit: contain;
  object-position: center center;
}
div.carousel .carousel__viewport .carousel__track .carousel__slide a[href*=".pdf"] img {
  width: 64px;
  height: 64px;
  object-fit: contain;
}
div.carousel .carousel__nav .carousel__button {
  display: flex;
  align-content: center;
  align-items: center;
  justify-content: center;
  border: none;
  width: 2em;
  height: 2em;
  line-height: 2em;
  cursor: pointer;
  border-radius: 50%;
  overflow: hidden;
  text-align: center;
  position: relative;
  z-index: var(--z_index30);
}
div.carousel .carousel__nav .carousel__button:before {
  pointer-events: none;
  z-index: var(--z_index20);
  content: "";
  background: currentColor;
  position: absolute;
  top: 50%;
  left: 50%;
  bottom: 50%;
  right: 50%;
  opacity: 0.2;
  border-radius: 100%;
  transition: all 0.5s;
}
div.carousel .carousel__nav .carousel__button:hover {
  opacity: 1;
}
div.carousel .carousel__nav .carousel__button:hover:before {
  pointer-events: none;
  opacity: 0.3;
  border-radius: 0%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.15s;
}
div.carousel .carousel__nav .carousel__button:active:before {
  background: var(--color_text_link);
  opacity: 0.6;
}
div.carousel .carousel__nav .carousel__button input:hover {
  opacity: 0.8;
}
div.carousel .carousel__nav .carousel__button .fa::before {
  font-size: 1.2em;
}
div.carousel .carousel__nav .carousel__button > * {
  font-size: 1.2em;
  float: initial !important;
}
div.carousel .carousel__nav .carousel__button {
  background: white;
  position: absolute;
  width: 3rem;
  height: 3rem;
}
div.carousel .carousel__dots {
  position: absolute;
  bottom: 0;
}

div.navigation-tab {
  margin-top: 0;
  display: flex;
  width: 100%;
  overflow-x: scroll;
  overflow-y: hidden;
  border: none;
  background-color: transparent;
  border-bottom: 1px solid var(--border-color-light);
  column-span: all !important;
}
div.navigation-tab a.tablink {
  flex-grow: 2;
  --height: auto;
  gap: 0.5em;
  text-decoration: none;
  display: flex;
  align-items: center;
  align-content: stretch;
  margin: 0;
  padding: 1em;
  height: var(--height, 100%);
}
div.navigation-tab a.tablink .badge {
  margin-top: 0;
  margin-bottom: 0;
  padding: 0.5em;
  line-height: 0.5em;
  vertical-align: middle;
  font-size: 0.8em;
  border: solid 1px;
  background: transparent;
  color: currentColor !important;
}
div.navigation-tab a.tablink {
  background: var(--colorbackbody);
}
div.navigation-tab a.tablink#addPartBtn {
  opacity: 1;
  flex-grow: 0;
  color: var(--color_text_link);
}
div.navigation-tab a.tablink.active {
  opacity: 1;
  color: var(--colortextlink);
}
div.navigation-tab a.tablink.active a {
  box-shadow: inset 0 -4px currentColor;
}
div.navigation-tab a.tablink.active:after {
  opacity: 0.1;
}
div.navigation-tab a.tablink.active {
  background: var(--colorbacklinepair1);
  pointer-events: none;
}

div.tabcontent {
  column-span: all !important;
  padding: 0;
  border: none;
}

[class*=boxfield-textarea] {
  flex-direction: column;
  align-items: flex-start;
  grid-row: span 10;
}
[class*=boxfield-textarea] .content {
  line-height: 1.4em;
}

#SuperCotroliaBoxParts > .content {
  padding: 0;
}
#SuperCotroliaBoxParts > .content > .tabcontent {
  padding: 1rem;
}
/*Multientity...*/
form#form_entity table {
  margin-top: 2rem;
}

#multicompany_entity_list_filter,
#multicompany_entity_list_length {
  margin: 0.5rem auto;
}
#multicompany_entity_list_filter label,
#multicompany_entity_list_length label {
  display: flex;
  align-items: center;
}

.multicompany_checker {
  padding: 1rem;
  display: block;
  background: var(--color_text_link);
  border-radius: var(--border-radius);
  color: var(--color_text_link_contrast);
  font-weight: bold;
  margin: 1rem auto;
}
.multicompany_checker .clearboth {
  display: block !important;
}

/*BASE DOLIBARR*/
/* Fichier destiné à recevoir tout le code custom pour le module TIERS de dolibarr */
/* Fiche Tier -> contact/adresse */
.publicnewticketform {
  overflow: auto;
}

/* Fichier destiné à recevoir tout le code custom pour le module ORDRES DE FABRICATION (MRP) de dolibarr */
form[action*="/mrp/mo_production.php"] * > .fichehalfleft > div > table > tbody > tr:not([name]) > td:nth-child(1) {
  width: 100% !important;
}
form[action*="/mrp/mo_production.php"] * > .fichehalfleft > div > table > tbody > tr > td {
  width: fit-content !important;
}
form[action*="/mrp/mo_production.php"] * > input.width50[list*=batch-] {
  width: fit-content !important;
}

body#mainbody > .phpdebugbar:not(.phpdebugbar-closed):not([aria-hidden]) {
  position: unset;
}

.swal2-container > .swal2-modal {
  max-width: 1024px;
  width: 100% !important;
}
.swal2-container > .swal2-modal #swal2-html-container table.tableforemailform {
  /* This is LOL... Stop use html table */
}
.swal2-container > .swal2-modal #swal2-html-container table.tableforemailform > tbody {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
.swal2-container > .swal2-modal #swal2-html-container table.tableforemailform > tbody > tr {
  flex-grow: 1;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 1rem;
}
.swal2-container > .swal2-modal #swal2-html-container table.tableforemailform > tbody > tr > td:first-of-type {
  flex: none;
}
.swal2-container > .swal2-modal #swal2-html-container table.tableforemailform > tbody > tr > td:last-of-type {
  display: flex;
  flex-wrap: wrap;
  flex-grow: 1;
}
.swal2-container > .swal2-modal #swal2-html-container table.tableforemailform > tbody > tr > td:last-of-type > *:not(.cke) {
  display: flex;
  flex-wrap: wrap;
  flex-grow: 1;
}

/*KANBAN*/
@media (max-width: 768px) {
  .kanban-grid-wrapper {
    display: flex !important;
    flex-direction: column !important;
  }
}
.kanban-grid-wrapper header {
  z-index: var(--z_index60) !important;
}

/*SUPERCOTROLIA*/
@media (max-width: 768px) {
  .logistic-content-container > .logistic-box-container > *:not(.logistic-stepper) {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
}

/*LAREPONSE*/
.tabs-article-multiselect {
  z-index: var(--z_index40) !important;
}

/*QUICKLIST*/
#id-right .quicklist-dropdown-content.show {
  display: flex !important;
  flex-direction: column;
  max-height: 264px;
}
#id-right div.div-table-responsive > table > tbody > tr.liste_titre_filter > td.liste_titre:last-child {
  z-index: var(--z_index60);
}
#id-right #searchFormList > div.div-table-responsive > table > tbody > tr:nth-child(2) {
  z-index: var(--z_index40);
}
#id-right #searchFormList > div.div-table-responsive > table > tbody > tr:nth-child(1) > td.liste_titre.maxwidthsearch {
  z-index: var(--z_index60);
}
#id-right #quicklistDropdown > .quicklist-action {
  border: none;
  border-radius: 0;
}
#id-right #quicklistDropdown > .quicklist-action:hover {
  background: #ccc;
}
#id-right #quicklistDropdown > input#quicklistInput {
  padding: 5px;
  border-radius: 0;
}
#id-right #quicklistDropdown > #quicklistElements > a {
  text-decoration: none;
}

input.select2-search__field {
  width: 100% !important;
}

div.quicklist-filter-list.centpercent {
  padding: 1rem;
  color: var(--colortexttitlenotab);
}
div.quicklist-filter-list.centpercent > .quicklist-filter-item {
  padding-top: 1rem;
}
div.quicklist-filter-list.centpercent > span#toggle-collapse {
  cursor: pointer;
}
div.quicklist-filter-list.centpercent > span#toggle-collapse > span[class*=fa-chevron] {
  margin-right: 0.5em;
}

@media (orientation: portrait) {
  .quicklist-button {
    margin-bottom: 6px;
  }
}
/*PACKINGLIST*/
form#searchFormList[action*="/custom/packinglist/packinglist.php"] {
  position: static !important;
}

.ui-dialog .ui-dialog-content {
  max-height: 70vh !important;
}

#dialog-confirm > div.confirmquestions > div.margintoponly > table > tbody > tr:not(.liste_titre) > td.nowrap {
  white-space: normal;
}

/*POSTIT*/
@media (orientation: portrait) {
  a[href*=createNote], div[id^=postit-].postit {
    display: none !important;
  }
}
div[data-edit-event="click.textEditor"] > textarea {
  color: rgba(0, 0, 0, 0.9) !important;
}
