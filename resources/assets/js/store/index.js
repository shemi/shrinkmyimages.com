import Vuex from 'vuex';

const vuex = new Vuex.Store({

    state : {
        queuedFiles: []
    },

    getters : {
        files: (state) => {
            return state.queuedFiles;
        },

        getFileByName: (state, getters) => (name) => {
            return getters.files.find(file => file.name === name);
        }

    },

    mutations : {
        addFile(state, file) {
            state.queuedFiles.push(file);
        },
        updateFile(state, file) {
            let index = state.queuedFiles.indexOf(file);

            state.queuedFiles[index] = file;
        }
        //TODO: implement remove file
        // removeFile(state, file) {
        //     state.queuedFiles.push(file);
        // }
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
