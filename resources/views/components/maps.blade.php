<div class="w-full h-[200px]" id="mapbox-map"></div>

@push('scripts')
    <script>
        let mapBoxHistorySite = null;

        const interval = setInterval(
            () => {
                if (mapboxgl) {
                    clearInterval(interval);
                    console.log('Mapbox loaded');
                    initMapbox();
                }
            },
            1000
        )

        const initMapbox = () => {
            mapboxgl.accessToken = '{{ config('app.mapbox_access_token') }}';
            const defaultLng = 112.0330392;
            const defaultLat = -6.8907313;

            const map = new mapboxgl.Map({
                container: 'mapbox-map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [defaultLng, defaultLat],
                zoom: 12
            });

            mapBoxHistorySite = map;

            setLngLat({lng: defaultLng, lat: defaultLat});

            setTimeout(() => {
                map.resize();
            }, 1000);

            const geolocate = new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true
                },
                trackUserLocation: true,
                showUserLocation:true
            });

            map.addControl(geolocate);

            let coordinates = {};
            geolocate.on('geolocate', (position) => {
                const { coords } = position
                coordinates = {
                    lat: coords.latitude,
                    lng: coords.longitude
                }

                setLngLat(coordinates);
            });

            const marker = new mapboxgl.Marker()
                .setLngLat([defaultLng, defaultLat])
                .addTo(map);

            map.addControl(new mapboxgl.NavigationControl());

            map.on('movestart', (e) => {
                marker.setLngLat(map.getCenter());
                setLngLat(marker.getLngLat());
            });

            map.on('move', (e) => {
                marker.setLngLat(map.getCenter());
                setLngLat(e=marker.getLngLat());
            });

            map.on('moveend', (e) => {
                marker.setLngLat(map.getCenter());
                setLngLat(marker.getLngLat());
            });
        }

        const setLngLat = (e) => {
            const lngLat = e;
            const lng = lngLat.lng;
            const lat = lngLat.lat;

            @this.set('longitude', lng);
            @this.set('latitude', lat);
        }
    </script>
@endpush
