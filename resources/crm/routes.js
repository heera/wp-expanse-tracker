const CrmApp = () => import(/* webpackChunkName: "crm" */ './Application');
const Dashboard = () => import(/* webpackChunkName: "crm" */ './Modules/Dashboard/Dashoard');
const Contacts = () => import(/* webpackChunkName: "crm" */ './Modules/Contacts/AllContacts');

export default {
    path: '/crm',
    component: CrmApp,
    children: [
        {
            name: 'crm-dashboard',
            path: '/',
            component: Dashboard,
            props: true
        },
        {
            name: 'crm-contacts',
            path: '/contacts',
            component: Contacts
        }
    ]
};
