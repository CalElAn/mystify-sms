require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import {
  DotsVerticalIcon,
  ViewListIcon,
  PhoneIcon,
  MailIcon,
} from '@heroicons/vue/solid';
// import { CogIcon } from '@heroicons/vue/outline';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import * as Sentry from '@sentry/vue';
import { BrowserTracing } from '@sentry/tracing';

import Layout from './Layouts/Layout.vue';
import Modal from '@/Components/Modal.vue';
import ProfilePicture from '@/Components/Users/ProfilePicture.vue';
import UserCard from '@/Components/Users/Card.vue';
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
  Head: Head,

  DotsVerticalIcon: DotsVerticalIcon,
  ViewListIcon: ViewListIcon,
  MenuItemsTransition: MenuItemsTransition,
  MenuItemButton: MenuItemButton,
  PhoneIcon: PhoneIcon,
  MailIcon: MailIcon,

  ProfilePicture: ProfilePicture,
  UserCard: UserCard,
  Modal: Modal,
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
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .mixin({ methods: { route } });

    _.forIn(baseComponents, function (value, key) {
      app.component(key, value);
    });

    if (process.env.MIX_APP_ENV === 'production') {
      Sentry.init({
        app,
        dsn: 'https://83fb401919714ebd96c98f079b778b7d@o1135206.ingest.sentry.io/6546328',
        integrations: [
          new BrowserTracing({
            routingInstrumentation: Sentry.vueRouterInstrumentation(router),
            tracingOrigins: ['localhost', 'my-site-url.com', /^\//],
          }),
        ],
        // Set tracesSampleRate to 1.0 to capture 100%
        // of transactions for performance monitoring.
        // We recommend adjusting this value in production
        tracesSampleRate: 0.0,
        logErrors: true,
      });
      //TODO delete later
      console.log('sentry set up');
    }

    app.mount(el);

    return app;
  },
});

InertiaProgress.init({ color: 'rgb(147 51 234)' });
