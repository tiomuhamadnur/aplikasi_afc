<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hierarchical Data Asset</title>

    <!-- jsTree CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
</head>

<body>
    <h1>Hierarchical Data Asset</h1>

    <!-- Element where the tree will be rendered -->
    <div id="categoryTree"></div>

    <!-- jQuery and jsTree -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

    <script type="text/javascript">
        $(function() {
            // Asumsikan data dari Blade dikirim sebagai string
            var dataString = @json($treeData);

            // Ubah string menjadi objek JSON
            var data = JSON.parse(dataString);

            $('#categoryTree').jstree({
                'core': {
                    'data': data // Gunakan data langsung dari controller
                },
                "checkbox": {
                    "keep_selected_style": false
                },
                "types": {
                    "default": {
                        "icon": "jstree-folder" // Ikon default untuk node yang punya children
                    },
                    "demo": {
                        "icon": "jstree-file" // Ikon untuk node yang tidak punya children
                    }
                },
                "plugins": ["checkbox", "types"], // Aktifkan plugin types
            });

            $('#categoryTree').on("loaded.jstree", function() {
                // Ambil semua node dalam bentuk flat list
                $('#categoryTree').jstree(true).get_json('#', {
                    flat: true
                }).forEach(function(node) {
                    // Pastikan 'children' didefinisikan sebelum mengecek panjangnya
                    if (!node.children || node.children.length === 0) {
                        // Ubah tipe node menjadi 'demo' jika tidak punya children
                        $('#categoryTree').jstree(true).set_type(node.id, 'demo');
                    }
                });
            });
        });
    </script>
</body>

</html>
