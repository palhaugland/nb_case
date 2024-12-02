# Nettbureau Case: Pipedrive Integrasjon

Denne integrasjonen oppretter en organisasjon, person og lead i Pipedrive basert på testdata. 

## Funksjoner
- Opprettelse av organisasjon, person og lead med nødvendige tilkoblinger.
- Håndtering av egendefinerte felter som `housing_type`, `property_size`, og `deal_type`.
- Sjekk etter eksisterende organisasjoner og personer før opprettelse.
- Logging av prosess og håndtering av API-feil.

---

## Installasjon

1. **Forbered systemet:**
   - Sørg for at PHP er installert med cURL aktivert:
     ```bash
     php --version
     ```

2. **Last ned prosjektet:**
   - Klon repoet og opprett nødvendige mapper:
     ```bash
     git clone <repo-url>
     mkdir src test logs
     ```

3. **Sett opp testdata:**
   - Rediger `test/test_data.json` for å legge til dine data.

---

## Kjøring

1. **Oppdater API-nøkkelen:**
   - Legg inn din Pipedrive API-nøkkel i `src/pipedrive_integration.php`:
     ```php
     $api_key = "din_api_nokkel";
     ```

2. **Kjør skriptet:**
   - Kjør skriptet fra terminalen:
     ```bash
     php src/pipedrive_integration.php
     ```

3. **Eksempelutdata:**
   - Hvis data finnes fra før:
     ```
     Organization already exists. Organization ID: 12345
     Person already exists. Person ID: 67890
     Lead created successfully. Lead ID: abcdef123456
     ```
   - Hvis nye data opprettes:
     ```
     Organization created successfully. Organization ID: 54321
     Person created successfully. Person ID: 09876
     Lead created successfully. Lead ID: fedcba654321
     ```

---

## Feilsøking

- **Loggfiler**:
  Sjekk `logs/error.log` for detaljer om feil.

- **Vanlige problemer**:
  - Sørg for at testdataene i `test/test_data.json` er riktige.
  - Valider at API-nøkkelen stemmer.
  - Sjekk at felt-ID-er i `$field_map` er oppdaterte.

---

## Filstruktur

- `src/pipedrive_integration.php`: Hovedskriptet.
- `test/test_data.json`: Testdata for å opprette organisasjon, person og lead.
- `logs/error.log`: Feillogg.

---

## Lisens
Dette prosjektet er utviklet for casebeskrivelsen og kan brukes til læring og evaluering.