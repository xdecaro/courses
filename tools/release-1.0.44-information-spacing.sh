#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OLD_VERSION="1.0.43"
VERSION="1.0.44"

python3 - "$ROOT" "$OLD_VERSION" "$VERSION" <<'PY'
from pathlib import Path
import json, re, sys

root = Path(sys.argv[1])
old_version = sys.argv[2]
version = sys.argv[3]

# Information CSS only. Restore the already-approved Courses integration spacing
# and vertically balance the closed Technical details row without changing its total height.
css_path = root / 'component/media/css/information.css'
css = css_path.read_text(encoding='utf-8')

old_integration = '  padding: 14px 0 14px;\n'
new_integration = '  padding: 14px 0 2px;\n'
if css.count(old_integration) != 1:
    raise SystemExit('Expected integration padding not found exactly once')
css = css.replace(old_integration, new_integration, 1)

old_details = '''.dci-details {\n  margin-top: 17px;\n  padding: 14px 16px 8px;\n'''
new_details = '''.dci-details {\n  margin-top: 17px;\n  padding: 11px 16px;\n'''
if css.count(old_details) != 1:
    raise SystemExit('Expected details spacing block not found exactly once')
css = css.replace(old_details, new_details, 1)

# Keep the 1.0.43 feedback bugfix: no reserved empty height.
feedback_block = css.split('.dci-feedback {', 1)[1].split('}', 1)[0]
if 'min-height' in feedback_block:
    raise SystemExit('Regression: dci-feedback reserves min-height')

css_path.write_text(css, encoding='utf-8')

# Bust only Information asset cache; other global Courses assets remain untouched.
asset_path = root / 'component/media/joomla.asset.json'
data = json.loads(asset_path.read_text(encoding='utf-8'))
data['version'] = version
found = {'style': False, 'script': False}
for asset in data.get('assets', []):
    if asset.get('name') == 'com_decarocourses.information' and asset.get('type') in found:
        asset['version'] = version
        found[asset['type']] = True
if not all(found.values()):
    raise SystemExit(f'Information assets not both found: {found}')
asset_path.write_text(json.dumps(data, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')

# Component manifest.
component_manifest = root / 'component/decarocourses.xml'
s = component_manifest.read_text(encoding='utf-8')
if f'<version>{old_version}</version>' not in s:
    raise SystemExit('Unexpected component base version')
s = s.replace(f'<version>{old_version}</version>', f'<version>{version}</version>', 1)
s = re.sub(r'  <!-- Release [^\n]+ -->\n', '', s, count=1)
s = s.replace(
    f'  <version>{version}</version>\n',
    f'  <version>{version}</version>\n  <!-- Release {version}: restore approved integration bottom spacing and balance Technical details vertically. -->\n',
    1,
)
component_manifest.write_text(s, encoding='utf-8')

# Package manifest.
package_manifest = root / 'package/pkg_decarocourses.xml'
s = package_manifest.read_text(encoding='utf-8')
if f'<version>{old_version}</version>' not in s:
    raise SystemExit('Unexpected package base version')
s = s.replace(f'<version>{old_version}</version>', f'<version>{version}</version>', 1)
old_nested = f'com_decarocourses_{old_version}.zip'
new_nested = f'com_decarocourses_{version}.zip'
if old_nested not in s:
    raise SystemExit('Unexpected nested package filename')
s = s.replace(old_nested, new_nested, 1)
package_manifest.write_text(s, encoding='utf-8')

# Joomla schema tracker marker, schema itself unchanged.
sql_path = root / f'component/admin/sql/updates/mysql/{version}.sql'
if sql_path.exists():
    raise SystemExit(f'{sql_path.name} already exists')
sql_path.write_text(f'-- Schema unchanged in {version}.\n', encoding='utf-8')

# README release metadata/changelog.
readme_path = root / 'README.md'
readme = readme_path.read_text(encoding='utf-8')
readme = re.sub(r'- Versione corrente: `[^`]+`', f'- Versione corrente: `{version}`', readme, count=1)
readme = re.sub(r'- ZIP componente: `com_decarocourses_[^`]+\.zip`', f'- ZIP componente: `com_decarocourses_{version}.zip`', readme, count=1)
readme = re.sub(r'- ZIP completo: `pkg_decarocourses_[^`]+\.zip`', f'- ZIP completo: `pkg_decarocourses_{version}.zip`', readme, count=1)
entry = f'''\n## {version}\n\nRifinitura mirata della vista **Informazioni** dopo verifica diretta con DevTools. In **Integrazioni** viene ripristinato il padding inferiore da 2 px già approvato in Courses, eliminando lo spazio vuoto introdotto successivamente. In **Diagnostica**, `Dettagli tecnici` mantiene la stessa altezza complessiva ma distribuisce il padding in modo simmetrico (`11px 16px`), centrando verticalmente il testo senza aumentare la card. Resta inoltre invariata la correzione 1.0.43 che non riserva altezza al feedback vuoto. Nessuna modifica a struttura, dati, Corsi, Edizioni, ACL o database.\n'''
m = re.search(r'\n## 1\.0\.\d+\n', readme)
if not m:
    raise SystemExit('README release anchor not found')
readme = readme[:m.start()] + entry + readme[m.start():]
readme_path.write_text(readme, encoding='utf-8')
PY

# Validation/guards.
php -r 'libxml_use_internal_errors(true); foreach (["component/decarocourses.xml","package/pkg_decarocourses.xml"] as $f) { if (simplexml_load_file($f) === false) { fwrite(STDERR, "Invalid XML: $f\n"); exit(1); } }'
php -r '$d=json_decode(file_get_contents("component/media/joomla.asset.json"), true); if (!is_array($d) || json_last_error() !== JSON_ERROR_NONE) exit(1);'

grep -q 'padding: 14px 0 2px;' component/media/css/information.css
grep -A4 '^\.dci-details {' component/media/css/information.css | grep -q 'padding: 11px 16px;'
if grep -A5 '^\.dci-feedback {' component/media/css/information.css | grep -q 'min-height'; then
  echo 'Regression: dci-feedback reserves min-height' >&2
  exit 1
fi

test -f "component/admin/sql/updates/mysql/${VERSION}.sql"

echo "Prepared Courses ${VERSION} Information spacing refinements"
