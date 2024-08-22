function objectToQueryString(params) {
    return Object.keys(params)
        .map(key => {
            const value = params[key];
            if (Array.isArray(value)) {
                return value
                    .map(v => `${encodeURIComponent(key)}=${encodeURIComponent(v)}`)
                    .join('&');
            }
            return `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
        })
        .join('&');
}

export default objectToQueryString;
