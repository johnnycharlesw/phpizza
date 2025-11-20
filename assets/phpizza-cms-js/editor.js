

window.addEventListener("DOMContentLoaded", (e)=>{
    let content_pd = document.getElementById("content_pd");
    require.config({ paths: { 'vs': '/load.php?t=monacoeditor_module&f=monaco-editor/min/vs' }});
    function initEditor(content){
        require(['vs/editor/editor.main'], function() {
            // Initialize the Monaco Editor
            var editor = monaco.editor.create(document.getElementById('editor_container'), {
                value: content,
                language: 'markdown'
            });
            
            // Function to update the hidden input with the editor's value
            function updateEditorContent() {
                content_pd.value = editor.getValue();
            }

            // Update the hidden input when the form is submitted
            document.querySelector('form').addEventListener('submit', function() {
                updateEditorContent();
            });
        }, function (err){
            console.log("Error loading Monaco Editor");
            console.log(err);
        });
    }
    
    initEditor(content_pd.value);
})