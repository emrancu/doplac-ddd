import {
    createRouter as _createRouter,
    createMemoryHistory,
} from 'vue-router'

import Home from "./../Home.vue";

let props = {};

const routes = [
    {
        path: "/",
        component: () => Home,
    }
]

export const createRouter = (appProps = {}) => {
    props = appProps;
    console.log(props,'props from rrrrr')
    routes.map(item=>{
      item.props =  props;
      return item
    })

    return _createRouter({
        history:  createMemoryHistory('/'),
        routes
    })
}