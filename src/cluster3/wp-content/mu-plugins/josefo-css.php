<?php
add_action('wp_head', function () {
	echo <<<'CSS'
<style>
body {
	background: #f5a623;
}
.wp-site-blocks {
	position: relative;
	background: #fff;
	max-width: 1100px;
	margin: 0 auto;
	min-height: 100vh;
	padding: 28px 20px 36px;
	box-shadow: 0 10px 32px rgba(80, 40, 0, 0.22);
}
.wp-site-blocks::before {
	content: "";
	position: absolute;
	inset: 0;
	z-index: 8;
	pointer-events: none;
	padding: 20px;
	background:
		radial-gradient(circle, #fff6d6 4px, #e8a317 7px, #7a4308 9px, #f5a623 11px, transparent 12px)
		10px 10px / 20px 20px;
	-webkit-mask:
		linear-gradient(#000 0 0) content-box,
		linear-gradient(#000 0 0);
	-webkit-mask-composite: xor;
	mask:
		linear-gradient(#000 0 0) content-box,
		linear-gradient(#000 0 0);
	mask-composite: exclude;
}
.wp-site-blocks::after {
	content: "";
	position: absolute;
	inset: 22px;
	z-index: 8;
	pointer-events: none;
	border: 2px solid #c47a10;
	box-shadow: inset 0 0 0 3px #ffe08a;
}
header.wp-block-template-part {
	min-height: 0 !important;
}
header.wp-block-template-part .wp-block-group {
	padding-top: 0.25rem !important;
	padding-bottom: 0.25rem !important;
}
main {
	padding-top: 2rem !important;
}
.wp-block-post-title {
	margin-top: 0.5rem !important;
	margin-bottom: 0.75rem !important;
	line-height: 1.2 !important;
	overflow: visible !important;
}
.entry-content {
	max-width: 900px;
	margin-left: auto;
	margin-right: auto;
	padding: 0 1.5rem 3rem;
}
.entry-content > .wp-block-image {
	max-width: 720px !important;
	width: 100% !important;
	margin: 0 auto 1rem auto !important;
}
.entry-content > .wp-block-image img {
	width: 100% !important;
	height: auto !important;
	border-radius: 12px;
}
.entry-content .wp-block-columns {
	align-items: center !important;
	justify-content: center !important;
	flex-wrap: nowrap !important;
	width: 100% !important;
	max-width: 900px !important;
	height: auto !important;
	margin: 0 auto 2.5rem auto !important;
	gap: 1.5rem !important;
}
.entry-content .wp-block-columns .wp-block-column {
	flex: 1 1 0 !important;
	max-width: none !important;
	min-width: 0 !important;
	display: flex;
	flex-direction: column;
	justify-content: center;
}
.entry-content .wp-block-image.alignleft,
.entry-content .wp-block-image.alignright {
	float: none !important;
	margin: 0 !important;
	display: block;
	width: 100%;
}
.entry-content .wp-block-columns img {
	width: 100% !important;
	height: auto !important;
	object-fit: cover;
	border-radius: 12px;
}
.entry-content .wp-block-columns:has(.wp-block-column:nth-child(2)) img {
	height: 240px !important;
}
.entry-content .wp-block-columns p {
	margin: 0;
	line-height: 1.55;
}
.entry-content .wp-block-buttons {
	display: flex !important;
	justify-content: center !important;
	align-items: center !important;
	width: 100% !important;
	max-width: 720px !important;
	margin-left: auto !important;
	margin-right: auto !important;
	flex-wrap: wrap !important;
	gap: 0.6rem !important;
	margin-top: 1.25rem !important;
	margin-bottom: 2rem !important;
}
.entry-content .wp-block-button__link {
	border-radius: 999px;
	padding: 0.55em 1.1em !important;
	font-size: 0.9rem !important;
	white-space: nowrap;
}
@media (max-width: 781px) {
	.entry-content .wp-block-columns {
		flex-wrap: wrap !important;
	}
	.entry-content .wp-block-columns .wp-block-column {
		flex: 1 1 100% !important;
	}
	.entry-content .wp-block-columns:has(.wp-block-column:nth-child(2)) img {
		height: 200px !important;
	}
}
</style>
CSS;
});
