require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { DotsVerticalIcon, ViewListIcon } from '@heroicons/vue/solid';
// import { CogIcon } from '@heroicons/vue/outline';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

import Layout from './Layouts/Layout.vue';
import Modal from '@/Components/Modal.vue';
import ProfilePicture from '@/Components/ProfilePicture.vue';
import MenuItemsTransition from '@/Components/MenuItemsTransition.vue';
import MenuItemButton from '@/Components/MenuItemButton.vue';

const appName =
  window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

const baseComponents = {
  Menu: Menu,
  MenuButton: MenuButton,
  MenuItems: MenuItems,
  MenuItem: MenuItem,
  Link: Link,
  DotsVerticalIcon: DotsVerticalIcon,
  ViewListIcon: ViewListIcon,
  MenuItemsTransition: MenuItemsTransition,
  MenuItemButton: MenuItemButton,
};

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => {
    const page = require(`./Pages/${name}`).default;
    if (!name.startsWith('Auth/') && name !== 'Welcome') {
      //TODO clean this up
      page.layout = page.layout || Layout;
    }
    return page;
  },
  setup({ el, app, props, plugin }) {
    const vueApp = createApp({ render: () => h(app, props) })
      // .component('MenuIcon', MenuIcon)
      // .component('CogIcon', CogIcon)
      .component('Modal', Modal)
      .component('ProfilePicture', ProfilePicture)
      .use(plugin)
      .mixin({ methods: { route } });

    _.forIn(baseComponents, function (value, key) {
      vueApp.component(key, value);
    });

    vueApp.mount(el);

    return vueApp;
  },
});

InertiaProgress.init({ color: 'rgb(147 51 234)' });
