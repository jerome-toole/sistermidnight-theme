import { writeFileSync } from 'node:fs';
import { resolve } from 'node:path';
import fg from 'fast-glob';

/**
 * Vite plugin to auto-generate component imports file.
 * Runs once on dev server start and on production builds.
 */
export default function generateComponentImportsPlugin() {
    let generated = false;

    // Define generateImports outside plugin object so it can be called
    const generateImports = () => {
        const componentStyles = fg.sync('components/*/styles.pcss');

        const imports = componentStyles
            .map((file) => {
                const relativePath = `../${file}`;
                return `@import "${relativePath}";`;
            })
            .join('\n');

        const content = `/* Auto-generated component imports - DO NOT EDIT MANUALLY */\n/* This file is automatically updated by vite-plugin-generate-component-imports.js */\n\n${imports}\n`;

        const outputPath = resolve(process.cwd(), 'assets/_components.pcss');
        writeFileSync(outputPath, content, 'utf-8');
    };

    return {
        name: 'vite-plugin-generate-component-imports',

        buildStart() {
            // Only run once during production builds
            if (!generated) {
                generateImports();
                generated = true;
            }
        },

        configureServer(server) {
            // Generate once when dev server starts
            generateImports();

            // Watch for new/deleted component styles in dev mode
            server.watcher.add('components/*/styles.pcss');

            server.watcher.on('add', (path) => {
                if (path.endsWith('/styles.pcss')) {
                    generateImports();
                }
            });

            server.watcher.on('unlink', (path) => {
                if (path.endsWith('/styles.pcss')) {
                    generateImports();
                }
            });

        },

        handleHotUpdate({ file, server }) {
            if (!file.match(/components\/[^/]+\/styles\.pcss$/)) return;

            // Component styles can't be standalone Vite modules (they need main.pcss context),
            // so we find main.pcss in the module graph and return it as the affected module.
            // Vite will invalidate it and send a css-update (injection) instead of full-reload.
            const affected = [];
            for (const mod of server.moduleGraph.idToModuleMap.values()) {
                if (mod.file?.endsWith('/assets/main.pcss')) {
                    server.moduleGraph.invalidateModule(mod);
                    affected.push(mod);
                }
            }
            return affected.length > 0 ? affected : undefined;
        },
    };
}
