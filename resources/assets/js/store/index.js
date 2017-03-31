import Vuex from 'vuex';

const vuex = new Vuex.Store({

    state : {
        appState: {
            user: {}
        },
        queuedFiles: [],
        pages: [],
    },

    getters : {
        user: (state) => {
            return state.appState.user;
        },

        files: (state) => {
            return state.queuedFiles;
        },

        webImagesPerShrink: (state) => {
            return state.appState.webImagesPerShrink;
        },

        getFileByName: (state, getters) => (name) => {
            return getters.files.find(file => file.name === name);
        },

        pages: (state) => {
            return state.pages;
        },

        getPageByName: (state, getters) => (name) => {
            return getters.pages.find(page => page.slug === name);
        }
    },

    mutations : {
        addFile(state, file) {
            state.queuedFiles.push(file);
        },

        setUser(state, user) {
            state.appState.user = user;
        },

        setAppState(state, appState) {
            state.appState = appState;
        },

        updateFile(state, file) {
            let index = state.queuedFiles.indexOf(file);

            state.queuedFiles[index] = file;
        },

        removeFile(state, file) {
            let index = state.queuedFiles.indexOf(file);

            if(index > -1) {
                state.queuedFiles.splice(index, 1);
            }
        },

        addPage(state, page) {
            state.pages.push(page);
        }
    },

    actions: {
        updateFile({commit}, file) {
            if(file.xhr && file.xhr.response) {
                let response = JSON.parse(file.xhr.response);
                file.response = response.data;
            }

            commit('updateFile', file);
        }

    },

    modules : {

    }

});

export default vuex;
