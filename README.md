# nb_case
Case for integrasjon og dataflyt

Denne PHP-integrasjonen oppretter en organisasjon, person og lead i Pipedrive basert på testdata.

# Filstruktur
- `src/pipedrive_lead_integration.php`: Hovedskriptet.
- `test/test_data.json`: Testdata for å validere funksjonaliteten.

## Oppsett
1. Installer PHP og aktiver cURL.
2. Klon repoet og plasser `src`, `test`, og `logs`-mappene i prosjektet.
3. Oppdater `test/test_data.json` med dine testdata.
4. Kjør scriptet:
   ```bash
   php src/pipedrive_integration.php
