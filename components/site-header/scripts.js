import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';
import SiteHeader from './scripts/SiteHeader.js';

dynamicElements.define('[data-site-header]', (element) => new SiteHeader(element));
