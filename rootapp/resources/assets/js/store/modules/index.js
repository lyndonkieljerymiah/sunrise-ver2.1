import Vue from 'vue';
import Vuex from 'vuex';

import villas from './villas';
import contracts from './contracts';
import bills from './bills';
import payments from './payments';
import expenditures from './expenditures';
import payees from "./payees";
import tenants from "./tenants";

import liveviews from "./liveviews";

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        villas,
        contracts,
        bills,
        payments,
        expenditures,
        payees,
        liveviews,
        tenants
    }

});

