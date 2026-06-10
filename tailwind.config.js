/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		'./templates/**/*.php',
		'./includes/**/*.php',
		'./lifex-project-gallery.php',
	],
	corePlugins: {
		// Disable preflight so the plugin does not reset browser/theme base styles.
		preflight: false,
	},
	theme: {
		extend: {},
	},
	plugins: [],
};
