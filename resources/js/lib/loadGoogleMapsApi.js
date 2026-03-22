let googleMapsPromise = null;

export const loadGoogleMapsAPI = () => {
    if (window.google?.maps) {
        return Promise.resolve(window.google);
    }

    if (googleMapsPromise) {
        return googleMapsPromise;
    }

    googleMapsPromise = new Promise((resolve, reject) => {
        const googleMapsKey = import.meta.env.VITE_GOOGLE_API_KEY;

        if (!googleMapsKey) {
            reject(new Error('Google Maps API key is missing.'));
            return;
        }

        const existingScript = document.querySelector('script[data-google-maps="true"]');

        if (existingScript) {
            if (window.google?.maps) {
                resolve(window.google);
                return;
            }

            existingScript.addEventListener('load', () => resolve(window.google));
            existingScript.addEventListener('error', () =>
                reject(new Error('Error loading Google Maps API'))
            );
            return;
        }

        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsKey}&libraries=places,geometry`;
        script.async = true;
        script.defer = true;
        script.dataset.googleMaps = 'true';

        script.onload = () => {
            if (window.google?.maps) {
                resolve(window.google);
            } else {
                reject(new Error('Google Maps loaded, but window.google.maps is unavailable.'));
            }
        };

        script.onerror = () => {
            reject(new Error('Error loading Google Maps API'));
        };

        document.head.appendChild(script);
    });

    return googleMapsPromise;
};
