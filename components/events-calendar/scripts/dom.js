/** createElement shorthand: h('a', { href, class }, 'text', childNode, …) */
export function h(tag, attrs = {}, ...children) {
    const element = document.createElement(tag);

    for (const [name, value] of Object.entries(attrs)) {
        if (value !== null && value !== undefined && value !== false) element.setAttribute(name, value);
    }

    element.append(...children.filter((child) => child !== null && child !== undefined && child !== false));

    return element;
}
