import Vue from 'vue';

Vue.filter('fileSize', function (bytes) {
    if(bytes == 0) {
        return '0 B';
    }

    let k = 1000,
        sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
});
