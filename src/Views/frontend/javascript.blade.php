// Javascript module for "{{ $definition->getHandle() }}"

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.initAxios = function (csrfTokenMeta, authTokenMeta) {
    if (csrfTokenMeta) {
        token = document.head.querySelector('meta[name="' + csrfTokenMeta + '"]');

    if (token)
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    else
        console.error('CSRF token not found: ' + csrfTokenMeta);
    }

    if (authTokenMeta) {
        token = document.head.querySelector('meta[name="' + authTokenMeta + '"]');

        if (token) {
            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token.content;
        } else {
            console.error('Auth token not found: ' + authTokenMeta);
        }
    }
};
window.initAxios('csrf-token', 'api-token');

@if($definition->isVue())

function vueData() {
@if(count($definition->getVueData()))
    return {!! json_encode($definition->getVueData(), JSON_PRETTY_PRINT) !!};
@else
    return {};
@endif
}

window.Vue = require('vue');

@if(count($definition->getVueComponents()))
@foreach($definition->getVueComponents() as $key => $path)
@if(strpos($path,'/') !== FALSE)
Vue.component('{{ $key }}', require('{{ $definition->relative($path) }}').default);
@else
Vue.component('{{ $key }}', require('{{ $path }}').default);
@endif
@endforeach
@endif

let v = new Vue({
    el: '{{ $definition->getVue() }}',
    data: vueData(),
});

@endif

@if(count($definition->getJs()))
@foreach($definition->getJs() as $path)
require('{{ $definition->relative($path) }}');
@endforeach
@endif



