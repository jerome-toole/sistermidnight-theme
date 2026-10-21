# Agent brief template (refinement batches)

Paste into every subagent prompt, then add the task block. Group TODO items by file ownership; run independent groups in parallel.

```
Sister Midnight WordPress theme (Gust). cwd is the theme root, branch build/new-site, local site https://sistermidnight.test, WP CLI --path=../../../, PHP errors in ../../debug.log.
Read .docs/BUILD-PLAN.md "Environment" and "Acceptance checks", then only the spec sections named below in .docs/_WEBSITE-SPEC.md (grep -n '^###' to find them) and the named reference screenshot(s) in .docs/design/ (figma-tree.txt has exact sizes/colours/fonts; design/assets/ has exported SVGs, posters and photos).
TASK: <items from .docs/TODO.md, verbatim> — you own <components/files>; do not edit <files owned by parallel agents>.
Verify in the browser with the chrome-devtools MCP (ToolSearch select:mcp__plugin_chrome-devtools-mcp_chrome-devtools__navigate_page,…take_snapshot,…take_screenshot,…evaluate_script,…resize_page): pages at 1440 and 500 against the reference, axe 0 violations (inject https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.10.2/axe.min.js), keyboard paths, no new debug.log lines. Need wp-admin? create a temp admin with WP CLI as AGENTS.md says and delete it after.
Other agents run in parallel. Touch only your files. Before `pnpm run build`: `until mkdir /tmp/sm-build.lock 2>/dev/null; do sleep 3; done; pnpm run build; rmdir /tmp/sm-build.lock` (always remove the lock). pint/lint only changed files. Commit only your files: `git add <paths> && git commit -m "<title>"`; on index.lock wait 3s and retry. Tick your TODO lines in .docs/TODO.md in that commit. Spec silent → simplest option, append `- [todo <slug>] decision — why` to .docs/BUILD-DECISIONS.md. Report in under 150 words: changed, verified, left.
```

After a batch: `git status` (commit strays), `pnpm run build`, curl the key pages, `tail ../../debug.log`, then summarise to the user with anything needing a human call.
