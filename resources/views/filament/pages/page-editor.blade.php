<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $template->name }} - {{ \App\Models\Setting::retrieve('app_name', config('app.name')) }}</title>
    <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="https://unpkg.com/grapesjs-tailwind"></script>
    <style>
        body,
        html {
            margin: 0;
            height: 100%;
        }

        .change-theme-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 5px;
        }

        .change-theme-button:focus {
            /* background-color: yellow; */
            outline: none;
            box-shadow: 0 0 0 2pt #c5c5c575;
        }

        /* Make blocks full width */
        .gjs-block {
            padding: 0 !important;
            width: 100% !important;
            min-height: auto !important;
        }

        /* Fit icons properly */
        .gjs-block svg {
            width: 100%;
        }

        .panel__top {
            padding: 0;
            width: 100%;
            display: flex;
            position: initial;
            justify-content: center;
            justify-content: space-between;
        }

        .panel__basic-actions {
            position: initial;
        }
    </style>
    @vite('resources/js/app.js')
</head>

<body>
    <div class="panel__top">
        <div class="panel__basic-actions"></div>
    </div>
    <div id="gjs">
        {!! $view !!}
    </div>

    <script>
        // Handle tailwind's use of slashes in css names
        const escapeName = (name) => `${name}`.trim().replace(/([^a-z0-9\w-:/]+)/gi, '-');

        const editor = grapesjs.init({
            container: '#gjs',
            height: '100%',
            fromElement: true,
            storageManager: false,
            selectorManager: {
                escapeName
            },
            plugins: ['grapesjs-tailwind'],
        });

        editor.Panels.addPanel({
            id: 'panel-top',
            el: '.panel__top',
        });
        editor.Panels.addPanel({
            id: 'basic-actions',
            el: '.panel__basic-actions',
            buttons: [{
                id: 'save',
                className: 'btn-save',
                label: 'Save',
                command: 'save-template',
            }],
        });

        const commands = editor.Commands;
            commands.add('save-template', (editor) => {
                const html = editor.getHtml();
                const css = editor.getCss();
                const js = editor.getJs();

                axios.post(window.location.href, {
                    html: html,
                    css: css,
                    js: js
                }).then(response => {
                    alert("Saved!");
                    window.location.href = '/admin/templates';
                }).catch(error => {
                    alert("Error saving template!");
                });

            });
        </script>
    </script>
</body>

</html>
