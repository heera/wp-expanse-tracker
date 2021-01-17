import Dashboard from './Components/Dashboard';
import Accounts from './Components/Accounts';
import Account from './Components/Account';
import Ledgers from './Components/Ledgers';
import Ledger from './Components/Ledger';
import Entries from './Components/Entries';
import Entry from './Components/Entry';
import Reports from './Components/Reports';

export default [
    {
        path: '*',
        name: 'default',
        redirect: '/'
    },
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
        meta: {
            title: `
                <div class='logo'>
                    <img style="width:40px;" src="${window.alphaAdmin.brand_logo}" />
                    Dashboard
                </div>
            `
        }
    },
    {
        path: '/accounts',
        name: 'accounts',
        component: Accounts,
        meta: {
            title: 'Accounts'
        }
    },
    {
        path: '/account',
        name: 'account',
        component: Account,
        meta: {
            parent: 'Accounts'
        }
    },
    {
        path: '/ledgers',
        name: 'ledgers',
        component: Ledgers,
        meta: {
            title: 'Ledgers'
        }
    },
    {
        path: '/ledger',
        name: 'ledger',
        component: Ledger,
        meta: {
            parent: 'Ledgers'
        }
    },
    {
        path: '/entries',
        name: 'entries',
        component: Entries,
        meta: {
            title: 'Entries'
        }
    },
    {
        path: '/entry',
        name: 'entry',
        component: Entry,
        meta: {
            parent: 'entries'
        }
    },
    {
        path: '/reports',
        name: 'reports',
        component: Reports,
        meta: {
            title: 'Reports'
        }
    }
];
