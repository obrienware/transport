<style>
  #cli-output {
    width: 100%;
    height: 300px;
    background: black;
    color: #0f0;
    padding: 10px;
    overflow-y: auto;
    white-space: pre-wrap;
    border: 1px solid #0f0;
    font-family: monospace;
  }

  #cli-input {
    width: 100%;
    background: black;
    color: #0f0;
    border: none;
    padding: 10px;
    font-size: 16px;
    font-family: monospace;
  }
</style>
<div id="cli-output"></div>
<input type="text" id="cli-input" placeholder="Enter a command..." autofocus>

<script>
  async function sendCommand(command) {
    if (!command.trim()) return;

    let outputDiv = $('#cli-output');
    outputDiv.append(`> ${command}\n`);

    $('#cli-input').val('');

    try {
      let response = await $.ajax({
        url: '/api/cli_backend.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          command: command
        })
      });
      outputDiv.append(response + "\n");
    } catch (error) {
      outputDiv.append("Error: " + error.statusText + "\n");
    }

    outputDiv.scrollTop(outputDiv[0].scrollHeight);
  }

  $(document).ready(() => {
    $('#cli-input').keypress(async function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        await sendCommand($(this).val());
      }
    });
  });
</script>