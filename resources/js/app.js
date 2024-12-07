import './bootstrap';
import Quill from 'quill';
import mapboxgl from 'mapbox-gl';
import Chart from 'chart.js/auto';
import 'mapbox-gl/dist/mapbox-gl.css';
import anime from 'animejs/lib/anime.es.js';
import 'animate.css';

window.Quill = Quill;
window.mapboxgl = mapboxgl;
window.Chart = Chart;
window.anime = anime;


const createAnimation = () => {
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    if (isMobile) {

        return;
    }

    anime({
        targets: '.head-title',
        translateX: 250,
        scale: 1.5,
        delay: 1000,
        duration:2000
    });

    anime({
        targets: '.head-text',
        translateX: 35,
        scale: 1.1,
        delay: 1000,
        duration:2000
    });
}

window.createAnimation = createAnimation;
