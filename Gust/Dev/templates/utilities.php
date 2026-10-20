<?php
/**
 * Utility & Pattern Classes Style Guide
 */
?>
<!-- ============================================
     PATTERNS (3-patterns)
     ============================================ -->

<section class="dev-kit__section alignfull flow">
    <h2 data-dev-ui>Patterns</h2>

    <!-- Buttons -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Buttons</h3>
        <small><code class="dev-kit__code">.btn</code> and variants. Colours follow the colour context.</small>
        <div class="dev-kit__demo">
            <div class="flex-list">
                <button class="btn">.btn</button>
                <button class="btn btn--theme-2">.btn--theme-2</button>
                <button class="btn btn--ghost">.btn--ghost</button>
                <button class="btn btn--small">.btn--small</button>
                <button class="btn btn--label">.btn--label</button>
                <button class="btn btn--arrow">.btn--arrow</button>
                <button class="btn btn--square">1</button>
                <button class="btn" disabled>disabled</button>
            </div>
        </div>
        <div class="dev-kit__demo color-context-blue">
            <div class="flex-list">
                <button class="btn">.btn</button>
                <button class="btn btn--theme-2">.btn--theme-2</button>
                <button class="btn btn--ghost">.btn--ghost</button>
                <button class="btn btn--square">1</button>
                <a href="#">A link</a>
            </div>
        </div>
    </div>

    <!-- Button Icons -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Button Icons</h3>
        <small>
            <code class="dev-kit__code">.btn__icon</code>
            <code class="dev-kit__code">.btn--icon</code>
            <code class="dev-kit__code">.btn--icon-before</code>
        </small>
        <style>
            /* Demo only: set the icon image (pseudo-element mask-image can't be set inline) */
            .demo-btn-icon { --btn--icon: url('<?= staticUrl('images/icons/close.svg') ?>'); }
            .demo-btn-icon-only::after   { mask-image: url('<?= staticUrl('images/icons/close.svg') ?>'); width: 1em; height: 1em; inset: 0; margin: auto; }
            .demo-btn-icon-before::before { mask-image: url('<?= staticUrl('images/icons/close.svg') ?>'); width: 1em; height: 1em; inset: 0; margin: auto; }
        </style>
        <div class="dev-kit__demo">
            <div class="flex-list" style="align-items: center;">
                <!-- .btn__icon: inline <span> inside .btn; icon driven by --btn--icon on the button -->
                <button class="btn demo-btn-icon">
                    With icon <span class="btn__icon"></span>
                </button>

                <!-- .btn--icon: icon-only button via ::after; text hidden by text-indent -->
                <button class="btn btn--square btn--icon demo-btn-icon-only" aria-label="Close"></button>

                <!-- .btn--icon-before: icon-only button via ::before -->
                <button class="btn btn--square btn--icon-before demo-btn-icon-before" aria-label="Close"></button>
            </div>
            <div style="margin-top: 0.75rem; font-size: 0.8125rem; color: var(--dev-muted); display: flex; flex-direction: column; gap: 0.35rem;">
                <div><code class="dev-kit__code">.btn__icon</code> — inline <code class="dev-kit__code">&lt;span&gt;</code> inside <code class="dev-kit__code">.btn</code>; icon set via <code class="dev-kit__code">--btn--icon</code> CSS var on the button.</div>
                <div><code class="dev-kit__code">.btn--icon</code> — icon-only; hides text via <code class="dev-kit__code">text-indent</code>, renders icon via <code class="dev-kit__code">::after</code>. Always add <code class="dev-kit__code">aria-label</code>.</div>
                <div><code class="dev-kit__code">.btn--icon-before</code> — same as above using <code class="dev-kit__code">::before</code> (frees <code class="dev-kit__code">::after</code> for other use).</div>
            </div>
        </div>
    </div>

    <!-- Button Resets -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Button Resets</h3>
        <small><code class="dev-kit__code">.button-reset</code> <code class="dev-kit__code">.button-reset-hard</code></small>
        <div class="dev-kit__demo">
            <div class="flex-list">
                <button>Default button</button>
                <button class="button-reset">.button-reset</button>
                <button class="button-reset-hard">.button-reset-hard</button>
            </div>
        </div>
    </div>

    <!-- Links -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Link Styles</h3>
        <small><code class="dev-kit__code">.link</code> variants</small>
        <div class="dev-kit__demo">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div><a href="#" class="link">.link (default)</a></div>
                <div><a href="#" class="link link--2">.link--2 (underline on hover)</a></div>
                <div><a href="#" class="link link--foreground">.link--foreground</a></div>
            </div>
        </div>
    </div>

    <!-- Typography -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Typography</h3>
        <small><code class="dev-kit__code">.type-*</code> styles</small>
        <div class="dev-kit__demo">
            <div class="type-hero">.type-hero</div>
            <div class="type-h1">.type-h1</div>
            <div class="type-h2">.type-h2</div>
            <div class="type-h3">.type-h3</div>
            <div class="type-h4">.type-h4</div>
            <div class="type-h5">.type-h5</div>
            <div class="type-h6">.type-h6</div>
            <div class="type-base">.type-base</div>
            <div class="type-label">.type-label</div>
            <div class="type-label-sm">.type-label-sm</div>
            <div class="type-meta">.type-meta</div>
            <div class="type-code">.type-code</div>
        </div>
    </div>

    <!-- Grid -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Grid</h3>
        <small><code class="dev-kit__code">.grid-simple</code> <code class="dev-kit__code">.grid-columns-*</code> <code class="dev-kit__code">.grid-auto</code></small>
        <div class="dev-kit__demo">
            <p style="margin-bottom: 0.5rem;"><strong>.grid-simple .sm:[--cols:2] .md:[--cols:3]</strong></p>
            <div class="grid-simple sm:[--cols:2] md:[--cols:3]">
                <div class="dev-kit__box">1</div>
                <div class="dev-kit__box">2</div>
                <div class="dev-kit__box">3</div>
                <div class="dev-kit__box">4</div>
                <div class="dev-kit__box">5</div>
                <div class="dev-kit__box">6</div>
            </div>
        </div>
        <div class="dev-kit__demo">
            <p style="margin-bottom: 0.5rem;"><strong>.grid-auto .[--col-min-width:150px]</strong> (auto-fills based on min-width)</p>
            <div class="grid-simple grid-auto [--col-min-width:150px]">
                <div class="dev-kit__box">Auto 1</div>
                <div class="dev-kit__box">Auto 2</div>
                <div class="dev-kit__box">Auto 3</div>
                <div class="dev-kit__box">Auto 4</div>
            </div>
        </div>
    </div>

    <!-- Flex Grid -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Flex Grid</h3>
        <small><code class="dev-kit__code">.flex-grid</code> <code class="dev-kit__code">.flex-grid-auto</code></small>
        <div class="dev-kit__demo">
            <p style="margin-bottom: 0.5rem;"><strong>.flex-grid</strong> (uses --cols)</p>
            <div class="flex-grid" style="--cols: 4;">
                <div class="dev-kit__box">1</div>
                <div class="dev-kit__box">2</div>
                <div class="dev-kit__box">3</div>
                <div class="dev-kit__box">4</div>
            </div>
        </div>
    </div>

    <!-- Flex List -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Flex List</h3>
        <small><code class="dev-kit__code">.flex-list</code></small>
        <div class="dev-kit__demo">
            <ul class="flex-list">
                <li class="dev-kit__box" style="padding: 0.5rem 1rem;">Item 1</li>
                <li class="dev-kit__box" style="padding: 0.5rem 1rem;">Item 2</li>
                <li class="dev-kit__box" style="padding: 0.5rem 1rem;">Item 3</li>
                <li class="dev-kit__box" style="padding: 0.5rem 1rem;">Longer Item 4</li>
            </ul>
        </div>
    </div>

    <!-- Form Inputs -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Form Inputs</h3>
        <small><code class="dev-kit__code">.input</code> <code class="dev-kit__code">.select</code></small>
        <div class="dev-kit__demo">
            <div style="display: grid; gap: 1rem; max-width: 400px;">
                <input type="text" class="input" placeholder=".input">
                <input type="text" class="input input--button-height" placeholder=".input--button-height">
                <select class="input select">
                    <option>.select</option>
                    <option>Option 2</option>
                    <option>Option 3</option>
                </select>
                <textarea class="input" rows="3" placeholder=".input (textarea)"></textarea>
            </div>
        </div>
    </div>

    <!-- Checkbox -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Checkbox</h3>
        <small><code class="dev-kit__code">.checkbox-field</code></small>
        <div class="dev-kit__demo">
            <div style="max-width: 400px;">
                <div class="checkbox-field">
                    <input type="checkbox" id="check1" name="check1">
                    <label for="check1">Checkbox option 1</label>
                </div>
                <div class="checkbox-field">
                    <input type="checkbox" id="check2" name="check2" checked>
                    <label for="check2">Checkbox option 2 (checked)</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Radio -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Radio</h3>
        <small><code class="dev-kit__code">.radio-field</code></small>
        <div class="dev-kit__demo">
            <div style="max-width: 400px;">
                <div class="radio-field">
                    <input type="radio" id="radio1" name="radiogroup" checked>
                    <label for="radio1">Radio option 1</label>
                </div>
                <div class="radio-field">
                    <input type="radio" id="radio2" name="radiogroup">
                    <label for="radio2">Radio option 2</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Blockquote -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Blockquote</h3>
        <small><code class="dev-kit__code">.blockquote</code></small>
        <div class="dev-kit__demo">
            <blockquote class="blockquote">
                <p>This is a styled blockquote with a decorative left border.</p>
                <cite>— Citation</cite>
            </blockquote>
        </div>
    </div>

    <!-- Tooltip -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Tooltip</h3>
        <small><code class="dev-kit__code">.tooltip</code></small>
        <div class="dev-kit__demo">
            <div style="padding-top: 1rem;">
                <span class="tooltip">This is a tooltip message with an arrow pointing up</span>
            </div>
        </div>
    </div>

    <!-- Prose -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Prose</h3>
        <small><code class="dev-kit__code">.prose</code> — adds consistent margins to content elements</small>
        <div class="dev-kit__demo">
            <div class="prose" style="max-width: 500px;">
                <p>First paragraph within .prose container.</p>
                <p>Second paragraph with automatic margin spacing applied.</p>
                <ul>
                    <li>List item 1</li>
                    <li>List item 2</li>
                </ul>
                <p>Another paragraph after the list.</p>
            </div>
        </div>
    </div>

    <!-- Focus Styles -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Focus Styles</h3>
        <small><code class="dev-kit__code">.focus-base</code></small>
        <div class="dev-kit__demo">
            <div class="flex-list">
                <button class="btn" style="outline: 2px dotted var(--color-foreground);">.focus-base (dotted outline)</button>
            </div>
        </div>
    </div>

    <!-- Cross Icon -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Cross Icon</h3>
        <small><code class="dev-kit__code">.cross</code></small>
        <div class="dev-kit__demo">
            <div class="flex-list">
                <button class="btn btn--square cross" style="--cross--size: 50%; --cross--color: currentColor;" aria-label="Close"></button>
                <span style="width: 24px; height: 24px; display: inline-block;" class="cross" style="--cross--size: 100%;"></span>
            </div>
        </div>
    </div>

    <!-- Mask Icon -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Mask Icon</h3>
        <small><code class="dev-kit__code">.mask-icon</code> — apply to a pseudo-element; sets <code class="dev-kit__code">background-color: currentColor</code> + mask properties. Set <code class="dev-kit__code">mask-image</code> and dimensions separately.</small>
        <style>
            /* Demo only: dimensions and icon image (mask-icon doesn't set these) */
            .demo-mask-icon { display: inline-block; width: 1.5rem; height: 1.5rem; mask-image: url('<?= staticUrl('images/icons/close.svg') ?>'); }
        </style>
        <div class="dev-kit__demo">
            <div class="flex-list" style="align-items: center;">
                <span class="mask-icon demo-mask-icon" style="color: var(--color-foreground);"></span>
                <span class="mask-icon demo-mask-icon" style="color: var(--color-accent);"></span>
                <span class="mask-icon demo-mask-icon" style="color: var(--color-brand-2);"></span>
            </div>
            <p style="margin-top: 0.75rem; font-size: 0.8125rem; color: var(--dev-muted);">Icon color is driven by <code class="dev-kit__code">currentColor</code> — set <code class="dev-kit__code">color</code> on the element to recolor it.</p>
        </div>
        <div class="dev-kit__demo" style="background: var(--dev-accent-bg); padding: 0.75rem 1rem; margin-top: 0.25rem;">
            <pre>.my-icon::after {
    @apply mask-icon;               /* bg: currentColor, mask-position/repeat/size */
    display: block;
    width: 1.25rem;
    height: 1.25rem;
    mask-image: url('/images/icons/close.svg');
    /* --mask-icon--size: 80%;      override mask-size */
}</pre>
        </div>
    </div>

    <!-- Responsive Embed -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Responsive Embed</h3>
        <small><code class="dev-kit__code">.responsive-embed</code></small>
        <div class="dev-kit__demo">
            <div class="responsive-embed" style="max-width: 400px; aspect-ratio: 16/9;">
                <div class="dev-kit__placeholder" style="display: flex; align-items: center; justify-content: center;">16:9 embed</div>
            </div>
        </div>
    </div>

    <!-- Media Embed -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Media Embed</h3>
        <small><code class="dev-kit__code">.media-embed</code> — grid container with figcaption styling</small>
        <div class="dev-kit__demo">
            <figure class="media-embed" style="max-width: 400px;">
                <div class="dev-kit__placeholder" style="aspect-ratio: 4/3;"></div>
                <figcaption>This is a figcaption</figcaption>
            </figure>
        </div>
    </div>
</section>

<!-- ============================================
     UTILITIES (4-utilities)
     ============================================ -->

<section class="dev-kit__section alignfull flow">
    <h2 data-dev-ui>Utilities</h2>

    <!-- Color Context -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Color Context</h3>
        <small><code class="dev-kit__code">.color-context-{color}</code> <code class="dev-kit__code">.has-{color}-background-color</code></small>
        <p style="margin: 0.5rem 0;">Sets background color, foreground color, focus color, and link colors based on the color's configuration.</p>
        <div class="dev-kit__demo" style="padding: 0; overflow: hidden;">
            <?php foreach (['accent', 'brand-2', 'background', 'white'] as $context) : ?>
                <div class="color-context-<?= $context ?>" style="padding: 1.5rem;">
                    <strong>.color-context-<?= $context ?></strong>
                    <p style="margin: 0.5rem 0;">Text with <a href="#">a link</a> inherits correct colors.</p>
                    <button class="btn btn--small">.btn</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Foreground Color -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Foreground Color</h3>
        <small><code class="dev-kit__code">.foreground-from-{color}</code></small>
        <p style="margin: 0.5rem 0;">Sets only the foreground color (text, links, focus) without changing background.</p>
        <div class="dev-kit__demo">
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div class="foreground-from-accent bg-blue">
                    <strong>.foreground-from-accent</strong>
                    <p style="margin: 0;">With <a href="#">a link</a></p>
                </div>
                <div class="foreground-from-blue bg-blue">
                    <strong>.foreground-from-blue</strong>
                    <p style="margin: 0;">With <a href="#">a link</a></p>
                </div>
                <div class="foreground-from-red bg-red">
                    <strong>.foreground-from-red</strong>
                    <p style="margin: 0;">With <a href="#">a link</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alignment -->
    <div class="dev-kit__subsection alignfull content-grid">
        <h3 data-dev-ui>Content Grid and Alignment Classes</h3>
        <small><code>.content-grid</code></small>
        <small><code class="dev-kit__code">.alignleft</code> <code class="dev-kit__code">.alignright</code> <code class="dev-kit__code">.aligncenter</code> <code class="dev-kit__code">.alignwide</code> <code class="dev-kit__code">.alignfull</code></small>

        <!-- alignwide + alignfull inside a content-grid / content-flow context -->
        <div class="dev-kit__demo content-flow" style="padding: 0; overflow: hidden;">
            <div class="content-grid" style="row-gap: 0.75rem; padding-block: 1.5rem;">
                <div class="dev-kit__box" style="padding: 0.75rem;">Default</div>
                <div class="alignwide dev-kit__box" style="padding: 0.75rem;"><code>.alignwide</code></div>
                <div class="alignfull dev-kit__box" style="padding: 0.75rem;"><code>.alignfull</code></div>
                <div class="aligncenter dev-kit__box" style="max-width: 200px;"><code>.aligncenter</code></div>
                <div class="alignleft dev-kit__box"><code>.alignleft</code></div>
                <div class="alignright dev-kit__box"><code>.alignright</code></div>
            </div>
        </div>
    </div>

    <!-- Content Width -->
    <div class="dev-kit__subsection alignfull content-grid">
        <h3 data-dev-ui class="alignwide">Content Width</h3>
        <small><code class="dev-kit__code">.content-width-*</code> <code class="dev-kit__code">.content-width-fluid-*</code> <code class="dev-kit__code">.content-width-full</code></small>
        <div class="dev-kit__demo flow">
            <div class="content-width-2xs dev-kit__box">.content-width-2xs</div>
            <div class="content-width-xs dev-kit__box">.content-width-xs</div>
            <div class="content-width-sm dev-kit__box">.content-width-sm</div>
            <div class="content-width-md dev-kit__box">.content-width-md</div>
            <div class="content-width-full dev-kit__box">.content-width-full</div>
        </div>
    </div>

    <!-- Max Width Fluid -->
    <div class="dev-kit__subsection alignfull content-grid">
        <h3 data-dev-ui class="alignwide">Content Width Fluid</h3>
        <small><code class="dev-kit__code">.content-width-fluid-*</code></small>
        <div class="dev-kit__demo flow">
            <div class="content-width-fluid-xs dev-kit__box">.content-width-fluid-xs</div>
            <div class="content-width-fluid-sm dev-kit__box">.content-width-fluid-sm</div>
            <div class="content-width-fluid-md dev-kit__box">.content-width-fluid-md</div>
            <div class="content-width-fluid-lg dev-kit__box">.content-width-fluid-lg</div>

        </div>
    </div>

    <!-- List Reset -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>List Reset</h3>
        <small><code class="dev-kit__code">.list-reset</code> <code class="dev-kit__code">.list-reset-hard</code></small>
        <div class="dev-kit__demo">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div>
                    <p><strong>Default:</strong></p>
                    <ul>
                        <li>Item 1</li>
                        <li>Item 2</li>
                    </ul>
                </div>
                <div>
                    <p><strong>.list-reset:</strong></p>
                    <ul class="list-reset">
                        <li>Item 1</li>
                        <li>Item 2</li>
                    </ul>
                </div>
                <div>
                    <p><strong>.list-reset-hard:</strong></p>
                    <ul class="list-reset-hard">
                        <li>Item 1</li>
                        <li>Item 2</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Margin Trim -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Margin Trim</h3>
        <small><code class="dev-kit__code">.margin-trim</code> <code class="dev-kit__code">.margin-trim-first</code> <code class="dev-kit__code">.margin-trim-last</code></small>
        <div class="dev-kit__demo">
            <div class="prose" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start;">
                <div style="border: 2px solid var(--color-accent); padding: 0.5rem;">
                    <p><strong>Without .margin-trim:</strong></p>
                    <p>Paragraph with margins</p>
                    <p>Another paragraph</p>
                </div>
                <div class="margin-trim" style="border: 2px solid var(--color-accent); padding: 0.5rem;">
                    <p><strong>With .margin-trim:</strong></p>
                    <p>Paragraph with margins</p>
                    <p>Another paragraph</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Img Fit -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Image Fit</h3>
        <small><code class="dev-kit__code">.img-fit</code> — container for object-fit: cover images</small>
        <div class="dev-kit__demo">
            <div class="img-fit" style="width: 200px; aspect-ratio: 16/9;">
                <div class="dev-kit__placeholder"></div>
            </div>
        </div>
    </div>

    <!-- Screen Reader -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>Screen Reader Text</h3>
        <small><code class="dev-kit__code">.screen-reader</code> <code class="dev-kit__code">.sr-only</code> <code class="dev-kit__code">.screen-reader-text</code></small>
        <div class="dev-kit__demo">
            <p>Visually hidden but accessible to screen readers:</p>
            <button class="btn">
                Download
                <span class="sr-only">(opens PDF in new window)</span>
            </button>
            <p style="margin-top: 0.5rem;"><small>The span with .sr-only contains "(opens PDF in new window)" but is visually hidden.</small></p>
        </div>
    </div>

    <!-- No Scroll -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>No Scroll</h3>
        <small><code class="dev-kit__code">.no-scroll</code> — overflow: hidden (for modals)</small>
        <div class="dev-kit__demo">
            <p>Apply to body when modal is open to prevent background scrolling.</p>
        </div>
    </div>

    <!-- No Focus -->
    <div class="dev-kit__subsection">
        <h3 data-dev-ui>No Focus</h3>
        <small><code class="dev-kit__code">.no-focus-be-careful</code> — removes focus outline (use carefully!)</small>
        <div class="dev-kit__demo">
            <p>Only use when providing alternative focus indication. Name reminds developers to be careful about accessibility.</p>
        </div>
    </div>
</section>
