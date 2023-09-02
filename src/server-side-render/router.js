import {
    createRouter as _createRouter,
    createMemoryHistory,
} from 'vue-router'

import ssrData from '../../../../../ssr.js'

const routes = [];

for (const key in ssrData.apps) { 
    routes.push({
        path: '/'+ key,
        component: ssrData.apps[key],
    })
}
 
let props = {};

export const createRouter = (appProps = {}) => {
    props = appProps;
    routes.map(item=>{
      item.props =  props;
      return item
    })

    return _createRouter({
        history:  createMemoryHistory('/'),
        routes
    })
}