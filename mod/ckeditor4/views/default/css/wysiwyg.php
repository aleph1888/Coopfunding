/* CSS for CKEditor content iframe */

<?php // The Elgg CSS reset forces the scrollbar which we don't want ?>
html, body {
	height: auto;
	margin: 0;
}

body {
	margin: 8px;
}

dt { font-weight: bold }
dd { margin: 0 0 1em 1em }

ul, ol {
	margin: 0 1.5em 1.5em 0;
	padding-left: 1.5em;
}
ul {
	list-style-type: disc;
}
ol {
	list-style-type: decimal;
}
table {
	border: 1px solid #ccc;
}
table td {
	border: 1px solid #ccc;
	padding: 3px 5px;
}
img {
	max-width: 100%;
	height: auto;
}