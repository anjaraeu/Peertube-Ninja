$('.dimmer').dimmer({closable: false});
var notyf = new Notyf();
var vm = new Vue({
    el: '.vue',
    data: {
        instancedata: null,
        instance: null,
        fetch: false,
        error: false
    },
    methods: {
        fetchinst: function(e) {
            console.log(e);
            e.preventDefault();
            this.instancedata = null;
            this.fetch = true;
            fetch('fetch.php?instance='+this.instance).then((data) => {
                if (!data.ok) this.error = true;
                else data.json().then((json) => {
                    if (json.error) {
                        this.error = true;
                    } else {
                        if (!json.config) this.error = true;
                        else this.instancedata = json;
                    }
                })
                this.fetch = false;
            }).catch((e) => {
                this.error = true;
                this.fetch = false;
            });
        }
    },
    watch: {
        fetch: function(now, old) {
            if (now == true) $('.dimmer').dimmer('show');
            else $('.dimmer').dimmer('hide');
        },
        error: function(now, old) {
            if (now == true) {
                notyf.alert('The ninja failed to get metrics ! Check console for details');
                this.error = false;
            }
        }
    }
});