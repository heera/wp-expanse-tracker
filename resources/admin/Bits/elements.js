import Vue from 'vue';
import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';

import {
    Menu,
    Loading,
    Row,
    Main,
    MenuItem,
    Table,
    TableColumn,
    ButtonGroup,
    Button,
    Input,
    InputNumber,
    MessageBox,
    Notification,
    Select,
    OptionGroup,
    Option,
    Icon,
    Card,
    Form,
    FormItem,
    Dialog,
    Col
} from 'element-ui';

Vue.use(Table);
Vue.use(TableColumn);
Vue.use(Select);
Vue.use(Button);
Vue.use(Option);
Vue.use(OptionGroup);
Vue.use(ButtonGroup);
Vue.use(Icon);
Vue.use(Menu);
Vue.use(MenuItem);
Vue.use(Loading.directive);
Vue.use(Table);
Vue.use(Input);
Vue.use(InputNumber);
Vue.use(ButtonGroup);
Vue.use(Row);
Vue.use(Main);
Vue.use(TableColumn);
Vue.use(Button);
Vue.use(Form);
Vue.use(FormItem);
Vue.use(Dialog);
Vue.use(Col);
Vue.use(Card);

Vue.prototype.$message = MessageBox.alert;
Vue.prototype.$notify = Notification;

locale.use(lang);

export default Vue;
