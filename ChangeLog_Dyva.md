# Backported from Dolibarr:
- Backport develop commit b9967552a9fa38c8fd6aef2fbd113100259bf851 : JS validation (now native in 23.0)
- Backport develop PR #33696 : new conf to remove break before country in pdf (now native in 23.0)
- Backport develop PR #33157 : page break handling in pdf_cornas (now native in 23.0)

# Dyva specific patches:
- pdf_cyan (propale) : line prices always stay on the page where the line starts.
  Two forced values of $showpricebeforepagebreak:
    * init, instead of getDolGlobalInt('MAIN_PDF_DATA_ON_FIRST_PAGE')
    * photo branch, when a too high product photo is moved to the next page
  The 20.0 patches on $nexYAfterDesc and on the footer-proximity threshold are no longer
  needed: 23.0 handles both natively via getMaxAfterColsLinePositionsData() and via the
  ($curY + 4) > ($this->page_hauteur - $heightforfooter) guard.
