# Changelog ROBIC

Client-specific customizations applied on top of Dolibarr core, branch `22.0_robic`
(based on `origin/22.0`).

Every code change is wrapped in the source files with `// DA027918 - START` /
`// DA027918 - END` markers so it can be located and re-applied after a core upgrade.

---

## DA027918 — 2026-06-11 — Fix VAT analysis table header overlap (invoice PDF)

**Affected option:** `PDF_INVOICE_SHOW_VAT_ANALYSIS` (per-rate VAT recap table at the
bottom of customer invoices).

**Symptom:** the header label `VATAmount` ("Montant de la TVA" in French) is too long for
its 25 mm column, so it wraps onto two lines. The wrapped second line ("TVA") overlapped
the first data row (e.g. the amount "27,89" was printed under the word "TVA").

**Root cause:** in `drawInfoTable()`, the four header cells were rendered with
`MultiCell(25, 4, ...)` (4 mm minimum height) and the data rows then started at
`$pdf->GetY()`, which only returns the bottom of the **last** header cell rendered
(`AmountTotal`, single line). When an earlier header (`VATAmount`) wrapped taller, the data
rows started too high and the header text overlapped them.

**Fix:** compute a uniform header height as the maximum `getStringHeight()` of the four
translated labels (4 mm floor), render every header cell at that height (so the grey
background is even too), and set the data-row start position to the **maximum** `GetY()` of
the four header cells (robustness guard).

**Files modified** (same fix in the three core invoice PDF models):

| File | Note |
|------|------|
| `htdocs/core/modules/facture/doc/pdf_crabe.modules.php` | Model used by ROBIC (`FACTURE_ADDON_PDF = crabe`, all invoices) |
| `htdocs/core/modules/facture/doc/pdf_sponge.modules.php` | Same latent bug |
| `htdocs/core/modules/facture/doc/pdf_octopus.modules.php` | Same latent bug |

**Verification:** `php -l` OK on the three files. Invoice `PROV30623` regenerated with the
`crabe` model in French and visually checked — the header now sits on two lines inside the
grey box and the data rows start below it, with no overlap.

**Upstream note:** this bug is also present in vanilla Dolibarr core v22 — good candidate
for a pull request to `github.com/Dolibarr/dolibarr`.
