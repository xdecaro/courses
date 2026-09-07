from pathlib import Path
import re

old = '1.0.36'
new = '1.0.37'
p = Path('component/media/css/information.css')
css = p.read_text(encoding='utf-8')

css = re.sub(
    r'^\.dci-page \{.*?^\}',
    '''.dci-page {
  --dc-text: #172033;
  --dc-muted: #667085;
  --dc-border: #e2e7ee;
  --dc-surface: #fff;
  --dc-surface-soft: #f8fafc;
  --dc-primary: #1358d0;
  --dc-success: #137a4f;
  --dc-success-soft: #eaf8f1;
  --dc-warning: #9a6200;
  --dc-warning-soft: #fff4d6;
  --dc-danger: #b42318;
  --dc-danger-soft: #fff0ef;
  --dc-muted-soft: #eef1f5;
  width: 100%;
  max-width: 1180px;
  margin: 0 auto;
  padding: 6px 0 34px;
  box-sizing: border-box;
  color: var(--dc-text);
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}''',
    css,
    count=1,
    flags=re.S | re.M,
)

dark = '''
[data-bs-theme="dark"] .dci-page,
:root[data-color-scheme="dark"] .dci-page {
  --dc-text: #f2f4f7;
  --dc-muted: #aab4c2;
  --dc-border: #343c48;
  --dc-surface: #1c222b;
  --dc-surface-soft: #151a21;
  --dc-primary: #7da8ff;
  --dc-success: #78dda9;
  --dc-success-soft: #173a2c;
  --dc-warning: #f6c96f;
  --dc-warning-soft: #483715;
  --dc-danger: #ff9188;
  --dc-danger-soft: #471f1d;
  --dc-muted-soft: #2a3039;
}

.dci-page *,
.dci-page *::before,
.dci-page *::after {
  box-sizing: border-box;
}
'''
if '[data-bs-theme="dark"] .dci-page,' not in css:
    end = css.find('}\n', css.find('.dci-page {')) + 2
    css = css[:end] + '\n' + dark + css[end:]

pairs = [
    ('.dci-page-header {\n  margin: 4px 0 22px;\n}', '.dci-page-header {\n  margin: 0 0 22px;\n}'),
    ('font: 700 30px/1.16 var(--dc-font);', 'font: 700 30px/1.16 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;'),
    ('  border-radius: 11px;\n  background: var(--dc-surface);\n  box-shadow: 0 1px 2px rgba(15, 23, 42, .03);', '  border-radius: 11px;\n  background: var(--dc-surface);'),
    ('  font-weight: 750;\n}\n\n.dci-grid', '  font-weight: 700;\n}\n\n.dci-grid'),
    ('  box-shadow: 0 1px 2px rgba(15, 23, 42, .025);', '  border: 1px solid var(--dc-border);\n  background: var(--dc-surface);\n  color: var(--dc-text);\n  box-shadow: 0 2px 8px rgba(15, 23, 42, .025);'),
    ('.dci-card .dc-card-head { margin-bottom: 10px; }', '.dci-card .dc-card-head {\n  display: flex;\n  align-items: flex-start;\n  justify-content: space-between;\n  gap: 14px;\n  margin-bottom: 12px;\n}'),
    ('  font: 700 18px/1.2 var(--dc-font);', '  font: 700 18px/1.2 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;'),
    ('  font-size: 10px;\n  line-height: 1.3;\n  letter-spacing: .09em;', '  font-size: 11px;\n  font-weight: 800;\n  line-height: normal;\n  letter-spacing: .085em;'),
    ('.dci-list { margin: 0; }', '.dci-list { margin: 5px 0 0; }'),
    ('grid-template-columns: minmax(135px, 42%) minmax(0, 1fr);', 'grid-template-columns: minmax(145px, 42%) minmax(0, 1fr);'),
    ('  gap: 14px;\n  min-height: 41px;', '  gap: 16px;\n  min-height: 40px;'),
    ('  margin-top: 14px;', '  margin-top: 15px;'),
    ('  min-height: 36px;\n  border-radius: 7px;', '  min-height: 38px;\n  border-radius: 8px;'),
    ('  margin: 0 0 10px;', '  margin: -2px 0 12px;'),
    ('  flex: 0 0 18px;', '  flex: 0 0 19px;'),
    ('  width: 18px;\n  height: 18px;', '  width: 19px;\n  height: 19px;'),
]
for a, b in pairs:
    if a not in css:
        raise SystemExit('CSS marker missing: ' + a[:80])
    css = css.replace(a, b, 1)

old_code = '''.dci-row code,
.dci-integration code {
  padding: 0;
  background: transparent;
  color: var(--dc-text);
  font-size: 11px;
}'''
new_code = '''.dci-row code,
.dci-integration code {
  color: inherit;
  overflow-wrap: anywhere;
}'''
if old_code not in css:
    raise SystemExit('code block marker missing')
css = css.replace(old_code, new_code, 1)

old_mobile = '''@media (max-width: 560px) {
  .dci-page { padding: 4px 0 28px; }
  .dci-page-header { margin-bottom: 16px; }
  .dci-page-header h1 { font-size: 24px; }'''
new_mobile = '''@media (max-width: 560px) {
  .dci-page { padding-bottom: 24px; }
  .dci-page-header { margin-bottom: 18px; }
  .dci-page-header h1 { font-size: 25px; }'''
if old_mobile not in css:
    raise SystemExit('mobile marker missing')
css = css.replace(old_mobile, new_mobile, 1)

warning = '''.dc-badge.is-warning {
  background: var(--dc-warning-soft);
  color: var(--dc-warning);
}'''
if '.dci-page .dc-badge.is-muted {' not in css:
    if warning not in css:
        raise SystemExit('warning marker missing')
    css = css.replace(
        warning,
        warning + '''

.dci-page .dc-badge.is-muted {
  background: var(--dc-muted-soft);
  color: #5e6978;
}
[data-bs-theme="dark"] .dci-page .dc-badge.is-muted,
:root[data-color-scheme="dark"] .dci-page .dc-badge.is-muted {
  color: #c3cbd6;
}''',
        1,
    )
p.write_text(css, encoding='utf-8')

langs = {
    'component/admin/language/it-IT/com_decarocourses.information.ini': 'COM_DECAROCOURSES_INFORMATION="Informazioni"',
    'component/admin/language/en-GB/com_decarocourses.information.ini': 'COM_DECAROCOURSES_INFORMATION="Information"',
    'component/admin/language/it-IT/com_decarocourses.ini': 'COM_DECAROCOURSES_INFORMATION="Informazioni"',
    'component/admin/language/en-GB/com_decarocourses.ini': 'COM_DECAROCOURSES_INFORMATION="Information"',
}
for name, line in langs.items():
    f = Path(name)
    text = f.read_text(encoding='utf-8')
    key = line.split('=', 1)[0]
    if not re.search(r'^' + re.escape(key) + r'=', text, flags=re.M):
        f.write_text(line + '\n' + text, encoding='utf-8')

for name in [
    'component/decarocourses.xml',
    'package/pkg_decarocourses.xml',
    'component/admin/src/Helper/InformationHelper.php',
    'component/admin/src/Helper/UiHelper.php',
    'component/media/joomla.asset.json',
]:
    f = Path(name)
    text = f.read_text(encoding='utf-8')
    if old not in text:
        raise SystemExit('version marker missing: ' + name)
    f.write_text(text.replace(old, new), encoding='utf-8')

Path('component/admin/sql/updates/mysql/1.0.37.sql').write_text('-- Schema unchanged in 1.0.37.\n', encoding='utf-8')

f = Path('README.md')
text = f.read_text(encoding='utf-8')
text = text.replace('- Versione corrente: `1.0.36`', '- Versione corrente: `1.0.37`', 1)
text = text.replace('- ZIP componente: `com_decarocourses_1.0.36.zip`', '- ZIP componente: `com_decarocourses_1.0.37.zip`', 1)
text = text.replace('- ZIP completo: `pkg_decarocourses_1.0.36.zip`', '- ZIP completo: `pkg_decarocourses_1.0.37.zip`', 1)
note = '''
## 1.0.37

Corretto il titolo nativo della toolbar Joomla nella vista **Informazioni**: `COM_DECAROCOURSES_INFORMATION` viene ora caricato esplicitamente e appare come **Informazioni / Information**. Completato inoltre l’allineamento visivo con **Forms 1.3.71** usando direttamente il CSS di riferimento: ritmo pagina/header, eyebrow 11 px, card-head, gap righe 16 px, label minima 145 px, bottoni, colori e bordi locali, dark mode e misure mobile. Le modifiche sono confinate alla vista Informazioni; Corsi, Edizioni, dati e logica applicativa restano invariati.
'''
marker = '\n## 1.0.36\n'
if '## 1.0.37' not in text:
    if marker not in text:
        raise SystemExit('README marker missing')
    text = text.replace(marker, note + marker, 1)
f.write_text(text, encoding='utf-8')

# One-shot files are removed before the release commit.
Path('.github/workflows/prepare-1.0.37.yml').unlink(missing_ok=True)
Path('tools/prepare_1_0_37.py').unlink(missing_ok=True)
