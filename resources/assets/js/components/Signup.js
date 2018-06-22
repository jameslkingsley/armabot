export default {
    render() {
        return this.$scopedSlots.default({
            email: this.email,
            error: this.error,
            signup: this.signup,
            complete: this.complete,
            inputAttrs: { value: this.email },
            inputEvents: {
                input: e => { this.email = e.target.value }
            }
        });
    },

    data() {
        return {
            email: null,
            error: null,
            complete: false
        };
    },

    methods: {
        signup() {
            ajax.post('/signup', { email: this.email })
                .then(r => this.complete = true)
                .catch(({ response }) => {
                    if (response.status === 422) {
                        this.error = response.data.errors.email[0];
                    } else {
                        this.error = response.data.message;
                    }
                });
        }
    }
}
