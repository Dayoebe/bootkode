@assets
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
            },
            svg: { fontCache: 'global' },
            startup: {
                pageReady() {
                    return MathJax.startup.defaultPageReady().then(() => {
                        document.dispatchEvent(new Event('mathjax-loaded'));
                    });
                },
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" async></script>
@endassets
