#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
VERSION="1.0.43"

python3 - "$ROOT" "$VERSION" <<'PY'
from pathlib import Path
import json, re, sys
root = Path(sys.argv[1])
version = sys.argv[2]

# 1) Diagnostics only: remove the reserved empty height that creates the blank area
# below the final actions. Keep the feedback margin/font so a real message still has
# normal spacing when it is actually displayed.
css_path = root / 'component/media/css/information.css'
css = css_path.read_text(encoding='utf-8')
old = '''.dci-feedback {\n  min-height: 18px;\n  margin-top: 7px;\n  color: var(--dc-muted);\n  font-size: 11px;\n}\n'''
new = '''.dci-feedback {\n  margin-top: 7px;\n  color: var(--dc-muted);\n  font-size: 11px;\n}\n'''
if old not in css:
    raise SystemExit('Expected .dci-feedback block not found; refusing broad CSS rewrite')
css_path.write_text(css.replace(old, new, 1), encoding='utf-8')

# 2) Bust only the Information asset cache. Other Courses assets are unchanged.
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

# 3) Version component/package without touching install structure.
component_manifest = root / 'component/decarocourses.xml'
s = component_manifest.read_text(encoding='utf-8')
s, n = re.subn(r'<version>[^<]+</version>', f'<version>{version}</version>', s, count=1)
if n != 1:
    raise SystemExit('Component version not found')
s = re.sub(r'  <!-- Release [^\n]+ -->\n', '', s, count=1)
s = s.replace(
    f'  <version>{version}</version>\n',
    f'  <version>{version}</version>\n  <!-- Release {version}: remove empty diagnostics feedback reservation; integrations unchanged. -->\n',
    1,
)
component_manifest.write_text(s, encoding='utf-8')

package_manifest = root / 'package/pkg_decarocourses.xml'
s = package_manifest.read_text(encoding='utf-8')
s, n = re.subn(r'<version>[^<]+</version>', f'<version>{version}</version>', s, count=1)
if n != 1:
    raise SystemExit('Package version not found')
s, n = re.subn(r'com_decarocourses_[0-9.]+\.zip', f'com_decarocourses_{version}.zip', s, count=1)
if n != 1:
    raise SystemExit('Nested component package name not found')
package_manifest.write_text(s, encoding='utf-8')

# 4) Every release must advance Joomla schema tracking even when schema is unchanged.
sql_path = root / f'component/admin/sql/updates/mysql/{version}.sql'
if sql_path.exists():
    raise SystemExit(f'{sql_path.name} already exists')
sql_path.write_text(f'-- Schema unchanged in {version}.\n', encoding='utf-8')

# 5) Keep repository release documentation coherent.
readme_path = root / 'README.md'
readme = readme_path.read_text(encoding='utf-8')
readme = re.sub(r'- Versione corrente: `[^`]+`', f'- Versione corrente: `{version}`', readme, count=1)
readme = re.sub(r'- ZIP componente: `com_decarocourses_[^`]+\.zip`', f'- ZIP componente: `com_decarocourses_{version}.zip`', readme, count=1)
readme = re.sub(r'- ZIP completo: `pkg_decarocourses_[^`]+\.zip`', f'- ZIP completo: `pkg_decarocourses_{version}.zip`', readme, count=1)
entry = f'''\n## {version}\n\nCorretto l’ultimo spazio vuoto sotto le azioni della sezione **Diagnostica** nella vista **Informazioni**. Il contenitore `.dci-feedback` non riserva più `min-height: 18px` quando non esiste alcun messaggio: la card termina quindi con lo stesso ritmo visivo verificato manualmente in browser. Il feedback continua a comparire normalmente quando viene usato `Copia diagnostica` o `Scarica .txt`. La sezione **Integrazioni**, già approvata, non viene modificata. Aggiornata inoltre la versione cache dei soli asset Informazioni per assicurare il caricamento immediato del CSS corretto dopo l’upgrade. Nessuna modifica a Corsi, Edizioni, dati, ACL o schema database.\n'''
anchor = '\n## 1.0.41\n'
if anchor not in readme:
    # 1.0.42 may already have been documented in a concurrent release; insert before first changelog.
    m = re.search(r'\n## 1\.0\.\d+\n', readme)
    if not m:
        raise SystemExit('README release anchor not found')
    readme = readme[:m.start()] + entry + readme[m.start():]
else:
    readme = readme.replace(anchor, entry + anchor, 1)
readme_path.write_text(readme, encoding='utf-8')
PY

# Local guards before commit.
php -r 'libxml_use_internal_errors(true); foreach (["component/decarocourses.xml","package/pkg_decarocourses.xml"] as $f) { if (simplexml_load_file($f) === false) { fwrite(STDERR, "Invalid XML: $f\n"); exit(1); } }'
php -r '$d=json_decode(file_get_contents("component/media/joomla.asset.json"), true); if (!is_array($d) || json_last_error() !== JSON_ERROR_NONE) exit(1);'
grep -A5 '^\.dci-feedback {' component/media/css/information.css | grep -q 'margin-top: 7px'
if grep -A5 '^\.dci-feedback {' component/media/css/information.css | grep -q 'min-height'; then
  echo 'dci-feedback still reserves min-height' >&2
  exit 1
fi
grep -q 'padding: 14px 0 14px;' component/media/css/information.css

echo "Prepared Courses $VERSION diagnostics feedback fix"
