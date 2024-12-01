// Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import '@mdi/font/css/materialdesignicons.css';
import { mdi } from 'vuetify/iconsets/mdi';

// Define light and dark themes
const lightTheme = {
    dark: false,
    colors: {
        primary: "#FFCF00",
        secondary: "#424242",
        accent: "#82B1FF",
        error: "#FF5252",
        info: "#2196F3",
        success: "#4CAF50",
        warning: "#FFC107",
        lightblue: "#14c6FF",
        yellow: "#FFCF00",
        pink: "#FF1976",
        orange: "#FF8657",
        magenta: "#C33AFC",
        darkblue: "#1E2D56",
        gray: "#909090",
        neutralgray: "#9BA6C1",
        green: "#2ED47A",
        red: "#FF5C4E",
        darkblueshade: "#308DC2",
        lightgray: "#BDBDBD",
        lightpink: "#FFCFE3",
        white: "#FFFFFF",
        muted: "#6c757d",
    },
};

const darkTheme = {
    dark: true,
    colors: {
        primary: "FFD600",
        secondary: "#121212",
        accent: "#03DAC6",
        error: "#CF6679",
        info: "#2196F3",
        success: "#4CAF50",
        warning: "#FFC107",
        lightblue: "#0D6EFD",
        yellow: "#FFD600",
        pink: "#FF4081",
        orange: "#FF6D00",
        magenta: "#E040FB",
        darkblue: "#1A237E",
        gray: "#B0BEC5",
        neutralgray: "#78909C",
        green: "#388E3C",
        red: "#D32F2F",
        darkblueshade: "#1565C0",
        lightgray: "#CFD8DC",
        lightpink: "#F8BBD0",
        white: "#ECEFF1",
        muted: "#9E9E9E",
    },
};

// Create Vuetify instance
const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: "darkTheme", // Start with light theme by default
        themes: {
            lightTheme,
            darkTheme,
        },
    },
    ssr: true, // Enable server-side rendering support
    icons: {
        defaultSet: 'mdi', // Use Material Design Icons
        sets: {
            mdi,
        },
    },
});

export default vuetify;