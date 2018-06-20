<?php $route = ($sf_request->getAttribute('sf_route')) ? $sf_request->getAttribute('sf_route')->getRawValue() : null; ?>
<?php $etablissement = null ?>
<?php $compte = null ?>

<?php if($route instanceof EtablissementRoute): ?>
    <?php $etablissement = $route->getEtablissement(); ?>
    <?php $compte = $etablissement->getCompte(); ?>
<?php endif; ?>
<?php if($route instanceof CompteRoute): ?>
    <?php $compte = $route->getCompte(); ?>
    <?php $etablissement = $compte->getEtablissementObj(); ?>
<?php endif; ?>

<nav id="menu_navigation" class="navbar navbar-default">
    <div class="navbar-header hidden-lg hidden-md">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo url_for('accueil') ?>"><img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiCiAgIGlkPSJzdmcyMTMiCiAgIHZlcnNpb249IjEuMiIKICAgdmlld0JveD0iMCAwIDEzMi45MiA4MS40MyIKICAgaGVpZ2h0PSI4MS40M3B0IgogICB3aWR0aD0iMTMyLjkycHQiPgogIDxtZXRhZGF0YQogICAgIGlkPSJtZXRhZGF0YTIxNyI+CiAgICA8cmRmOlJERj4KICAgICAgPGNjOldvcmsKICAgICAgICAgcmRmOmFib3V0PSIiPgogICAgICAgIDxkYzpmb3JtYXQ+aW1hZ2Uvc3ZnK3htbDwvZGM6Zm9ybWF0PgogICAgICAgIDxkYzp0eXBlCiAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vcHVybC5vcmcvZGMvZGNtaXR5cGUvU3RpbGxJbWFnZSIgLz4KICAgICAgICA8ZGM6dGl0bGU+PC9kYzp0aXRsZT4KICAgICAgPC9jYzpXb3JrPgogICAgPC9yZGY6UkRGPgogIDwvbWV0YWRhdGE+CiAgPGRlZnMKICAgICBpZD0iZGVmczU4Ij4KICAgIDxnCiAgICAgICBpZD0iZzUzIj4KICAgICAgPHN5bWJvbAogICAgICAgICBpZD0iZ2x5cGgwLTAiCiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIj4KICAgICAgICA8cGF0aAogICAgICAgICAgIGlkPSJwYXRoMiIKICAgICAgICAgICBkPSJNIDMuMjUgMCBMIDMuMjUgLTYuMDkzNzUgTCAwLjc5Njg3NSAtNi4wOTM3NSBMIDAuNzk2ODc1IDAgWiBNIDAuOTY4NzUgLTAuMTcxODc1IEwgMC45Njg3NSAtNS45MjE4NzUgTCAzLjA3ODEyNSAtNS45MjE4NzUgTCAzLjA3ODEyNSAtMC4xNzE4NzUgWiBNIDAuOTY4NzUgLTAuMTcxODc1ICIKICAgICAgICAgICBzdHlsZT0ic3Ryb2tlOm5vbmU7IiAvPgogICAgICA8L3N5bWJvbD4KICAgICAgPHN5bWJvbAogICAgICAgICBpZD0iZ2x5cGgwLTEiCiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIj4KICAgICAgICA8cGF0aAogICAgICAgICAgIGlkPSJwYXRoNSIKICAgICAgICAgICBkPSJNIDQuOTM3NSAwIEwgNS45NTMxMjUgMCBMIDMuMDQ2ODc1IC02LjQ2ODc1IEwgMC4wMzEyNSAwIEwgMS4wMTU2MjUgMCBMIDEuNzAzMTI1IC0xLjQ4NDM3NSBMIDQuMzEyNSAtMS40ODQzNzUgWiBNIDIuMDc4MTI1IC0yLjM0Mzc1IEwgMy4wMzEyNSAtNC40MDYyNSBMIDMuOTM3NSAtMi4zNDM3NSBaIE0gMi4wNzgxMjUgLTIuMzQzNzUgIgogICAgICAgICAgIHN0eWxlPSJzdHJva2U6bm9uZTsiIC8+CiAgICAgIDwvc3ltYm9sPgogICAgICA8c3ltYm9sCiAgICAgICAgIGlkPSJnbHlwaDAtMiIKICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiPgogICAgICAgIDxwYXRoCiAgICAgICAgICAgaWQ9InBhdGg4IgogICAgICAgICAgIGQ9Ik0gMS43MDMxMjUgLTMuOTUzMTI1IEMgMS4zNTkzNzUgLTMuOTUzMTI1IDEuMDc4MTI1IC0zLjg0Mzc1IDAuODQzNzUgLTMuNjI1IEMgMC42MDkzNzUgLTMuNDA2MjUgMC41IC0zLjEyNSAwLjUgLTIuNzk2ODc1IEMgMC41IC0yLjUzMTI1IDAuNTYyNSAtMi4zMjgxMjUgMC43MDMxMjUgLTIuMTcxODc1IEMgMC44MTI1IC0yLjA0Njg3NSAwLjk4NDM3NSAtMS45MjE4NzUgMS4yNSAtMS43OTY4NzUgQyAxLjM0Mzc1IC0xLjc1IDEuNDM3NSAtMS43MDMxMjUgMS41MTU2MjUgLTEuNjcxODc1IEMgMS42MDkzNzUgLTEuNjI1IDEuNzAzMTI1IC0xLjU3ODEyNSAxLjc4MTI1IC0xLjUzMTI1IEMgMi4wMTU2MjUgLTEuMzkwNjI1IDIuMTI1IC0xLjI1IDIuMTI1IC0xLjA5Mzc1IEMgMi4xMjUgLTAuODEyNSAxLjk4NDM3NSAtMC42NzE4NzUgMS42ODc1IC0wLjY3MTg3NSBDIDEuNTMxMjUgLTAuNjcxODc1IDEuNDA2MjUgLTAuNzM0Mzc1IDEuMjk2ODc1IC0wLjgyODEyNSBDIDEuMjUgLTAuODc1IDEuMTU2MjUgLTEgMS4wNjI1IC0xLjE3MTg3NSBMIDAuMjk2ODc1IC0wLjgyODEyNSBDIDAuNTYyNSAtMC4yMDMxMjUgMS4wMzEyNSAwLjEwOTM3NSAxLjY3MTg3NSAwLjEwOTM3NSBDIDIuMDQ2ODc1IDAuMTA5Mzc1IDIuMzU5Mzc1IC0wLjAxNTYyNSAyLjY0MDYyNSAtMC4yNSBDIDIuOTA2MjUgLTAuNSAzLjAzMTI1IC0wLjgxMjUgMy4wMzEyNSAtMS4xNzE4NzUgQyAzLjAzMTI1IC0xLjQ2ODc1IDIuOTUzMTI1IC0xLjcwMzEyNSAyLjc5Njg3NSAtMS44NTkzNzUgQyAyLjY0MDYyNSAtMi4wMzEyNSAyLjMxMjUgLTIuMjE4NzUgMS44NDM3NSAtMi40MjE4NzUgQyAxLjUgLTIuNTYyNSAxLjMyODEyNSAtMi43MTg3NSAxLjMyODEyNSAtMi44NzUgQyAxLjMyODEyNSAtMi45NTMxMjUgMS4zNTkzNzUgLTMuMDE1NjI1IDEuNDIxODc1IC0zLjA2MjUgQyAxLjQ4NDM3NSAtMy4xMjUgMS41NDY4NzUgLTMuMTU2MjUgMS42MjUgLTMuMTU2MjUgQyAxLjc5Njg3NSAtMy4xNTYyNSAxLjk1MzEyNSAtMy4wMzEyNSAyLjA2MjUgLTIuNzk2ODc1IEwgMi43OTY4NzUgLTMuMTg3NSBDIDIuNTQ2ODc1IC0zLjY4NzUgMi4xODc1IC0zLjk1MzEyNSAxLjcwMzEyNSAtMy45NTMxMjUgWiBNIDEuNzAzMTI1IC0zLjk1MzEyNSAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICAgIDxzeW1ib2wKICAgICAgICAgaWQ9ImdseXBoMC0zIgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDExIgogICAgICAgICAgIGQ9Ik0gMC45Njg3NSAtMC40ODQzNzUgQyAxLjM0Mzc1IC0wLjA5Mzc1IDEuODI4MTI1IDAuMTA5Mzc1IDIuNDA2MjUgMC4xMDkzNzUgQyAyLjk4NDM3NSAwLjEwOTM3NSAzLjQ4NDM3NSAtMC4wNzgxMjUgMy44NzUgLTAuNDY4NzUgQyA0LjI4MTI1IC0wLjg1OTM3NSA0LjQ2ODc1IC0xLjM0Mzc1IDQuNDY4NzUgLTEuOTIxODc1IEMgNC40Njg3NSAtMi40ODQzNzUgNC4yODEyNSAtMi45Njg3NSAzLjg5MDYyNSAtMy4zNTkzNzUgQyAzLjQ4NDM3NSAtMy43NSAzIC0zLjk1MzEyNSAyLjQyMTg3NSAtMy45NTMxMjUgQyAxLjg1OTM3NSAtMy45NTMxMjUgMS4zNzUgLTMuNzUgMC45ODQzNzUgLTMuMzc1IEMgMC41NzgxMjUgLTIuOTY4NzUgMC4zNzUgLTIuNSAwLjM3NSAtMS45NTMxMjUgQyAwLjM3NSAtMS4zNTkzNzUgMC41NzgxMjUgLTAuODc1IDAuOTY4NzUgLTAuNDg0Mzc1IFogTSAxLjU5Mzc1IC0yLjgxMjUgQyAxLjgxMjUgLTMuMDMxMjUgMi4wNzgxMjUgLTMuMTU2MjUgMi40MjE4NzUgLTMuMTU2MjUgQyAyLjc2NTYyNSAtMy4xNTYyNSAzLjA0Njg3NSAtMy4wMzEyNSAzLjI1IC0yLjgxMjUgQyAzLjQ2ODc1IC0yLjU3ODEyNSAzLjU3ODEyNSAtMi4yODEyNSAzLjU3ODEyNSAtMS45MjE4NzUgQyAzLjU3ODEyNSAtMS41NDY4NzUgMy40Njg3NSAtMS4yNSAzLjI1IC0xLjAxNTYyNSBDIDMuMDQ2ODc1IC0wLjc5Njg3NSAyLjc2NTYyNSAtMC42NzE4NzUgMi40MjE4NzUgLTAuNjcxODc1IEMgMi4wNzgxMjUgLTAuNjcxODc1IDEuNzk2ODc1IC0wLjc5Njg3NSAxLjU5Mzc1IC0xLjAxNTYyNSBDIDEuMzkwNjI1IC0xLjI1IDEuMjgxMjUgLTEuNTQ2ODc1IDEuMjgxMjUgLTEuOTM3NSBDIDEuMjgxMjUgLTIuMjk2ODc1IDEuMzkwNjI1IC0yLjU3ODEyNSAxLjU5Mzc1IC0yLjgxMjUgWiBNIDEuNTkzNzUgLTIuODEyNSAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICAgIDxzeW1ib2wKICAgICAgICAgaWQ9ImdseXBoMC00IgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDE0IgogICAgICAgICAgIGQ9Ik0gMi40ODQzNzUgLTMuOTUzMTI1IEMgMS44OTA2MjUgLTMuOTUzMTI1IDEuMzkwNjI1IC0zLjc1IDAuOTg0Mzc1IC0zLjM1OTM3NSBDIDAuNTc4MTI1IC0yLjk2ODc1IDAuMzc1IC0yLjQ4NDM3NSAwLjM3NSAtMS45MDYyNSBDIDAuMzc1IC0xLjMyODEyNSAwLjU3ODEyNSAtMC44NDM3NSAwLjk4NDM3NSAtMC40Njg3NSBDIDEuMzc1IC0wLjA3ODEyNSAxLjg3NSAwLjEwOTM3NSAyLjQ2ODc1IDAuMTA5Mzc1IEMgMi44MjgxMjUgMC4xMDkzNzUgMy4xODc1IDAuMDE1NjI1IDMuNTQ2ODc1IC0wLjE3MTg3NSBMIDMuNTQ2ODc1IC0xLjM0Mzc1IEMgMy4zNDM3NSAtMS4wNzgxMjUgMy4xNTYyNSAtMC45MDYyNSAzIC0wLjgyODEyNSBDIDIuODI4MTI1IC0wLjczNDM3NSAyLjY0MDYyNSAtMC42NzE4NzUgMi40Mzc1IC0wLjY3MTg3NSBDIDIuMDkzNzUgLTAuNjcxODc1IDEuODEyNSAtMC43OTY4NzUgMS42MDkzNzUgLTEuMDMxMjUgQyAxLjM5MDYyNSAtMS4yNjU2MjUgMS4yODEyNSAtMS41NjI1IDEuMjgxMjUgLTEuOTIxODc1IEMgMS4yODEyNSAtMi4yNjU2MjUgMS4zOTA2MjUgLTIuNTYyNSAxLjYwOTM3NSAtMi43OTY4NzUgQyAxLjg0Mzc1IC0zLjAzMTI1IDIuMTA5Mzc1IC0zLjE1NjI1IDIuNDUzMTI1IC0zLjE1NjI1IEMgMi42NzE4NzUgLTMuMTU2MjUgMi44NTkzNzUgLTMuMTA5Mzc1IDMgLTMgQyAzLjE3MTg3NSAtMi45MjE4NzUgMy4zNDM3NSAtMi43NSAzLjU0Njg3NSAtMi41IEwgMy41NDY4NzUgLTMuNjcxODc1IEMgMy4yMTg3NSAtMy44NTkzNzUgMi44NTkzNzUgLTMuOTUzMTI1IDIuNDg0Mzc1IC0zLjk1MzEyNSBaIE0gMi40ODQzNzUgLTMuOTUzMTI1ICIKICAgICAgICAgICBzdHlsZT0ic3Ryb2tlOm5vbmU7IiAvPgogICAgICA8L3N5bWJvbD4KICAgICAgPHN5bWJvbAogICAgICAgICBpZD0iZ2x5cGgwLTUiCiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIj4KICAgICAgICA8cGF0aAogICAgICAgICAgIGlkPSJwYXRoMTciCiAgICAgICAgICAgZD0iTSAwLjU2MjUgLTMuODQzNzUgTCAwLjU2MjUgMCBMIDEuNDUzMTI1IDAgTCAxLjQ1MzEyNSAtMy44NDM3NSBaIE0gMC41OTM3NSAtNS4wMzEyNSBDIDAuNzAzMTI1IC00LjkwNjI1IDAuODQzNzUgLTQuODU5Mzc1IDEuMDE1NjI1IC00Ljg1OTM3NSBDIDEuMTU2MjUgLTQuODU5Mzc1IDEuMjk2ODc1IC00LjkwNjI1IDEuNDA2MjUgLTUuMDMxMjUgQyAxLjUzMTI1IC01LjE0MDYyNSAxLjU3ODEyNSAtNS4yODEyNSAxLjU3ODEyNSAtNS40Mzc1IEMgMS41NzgxMjUgLTUuNTkzNzUgMS41MzEyNSAtNS43MzQzNzUgMS40MDYyNSAtNS44NDM3NSBDIDEuMjk2ODc1IC01Ljk1MzEyNSAxLjE1NjI1IC02LjAxNTYyNSAxIC02LjAxNTYyNSBDIDAuODQzNzUgLTYuMDE1NjI1IDAuNzAzMTI1IC01Ljk1MzEyNSAwLjU5Mzc1IC01Ljg0Mzc1IEMgMC40ODQzNzUgLTUuNzM0Mzc1IDAuNDIxODc1IC01LjU5Mzc1IDAuNDIxODc1IC01LjQzNzUgQyAwLjQyMTg3NSAtNS4yODEyNSAwLjQ4NDM3NSAtNS4xNDA2MjUgMC41OTM3NSAtNS4wMzEyNSBaIE0gMC41OTM3NSAtNS4wMzEyNSAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICAgIDxzeW1ib2wKICAgICAgICAgaWQ9ImdseXBoMC02IgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDIwIgogICAgICAgICAgIGQ9Ik0gMy4zOTA2MjUgLTMuMzkwNjI1IEMgMy4wMzEyNSAtMy43NjU2MjUgMi42NDA2MjUgLTMuOTUzMTI1IDIuMTg3NSAtMy45NTMxMjUgQyAxLjY3MTg3NSAtMy45NTMxMjUgMS4yMzQzNzUgLTMuNzUgMC44OTA2MjUgLTMuMzc1IEMgMC41NDY4NzUgLTIuOTg0Mzc1IDAuMzc1IC0yLjUxNTYyNSAwLjM3NSAtMS45Mzc1IEMgMC4zNzUgLTEuMzQzNzUgMC41NDY4NzUgLTAuODU5Mzc1IDAuODkwNjI1IC0wLjQ2ODc1IEMgMS4yNSAtMC4wNzgxMjUgMS42ODc1IDAuMTA5Mzc1IDIuMjE4NzUgMC4xMDkzNzUgQyAyLjY0MDYyNSAwLjEwOTM3NSAzLjAzMTI1IC0wLjA2MjUgMy4zOTA2MjUgLTAuNDA2MjUgTCAzLjM5MDYyNSAwIEwgNC4yODEyNSAwIEwgNC4yODEyNSAtMy44NDM3NSBMIDMuMzkwNjI1IC0zLjg0Mzc1IFogTSAxLjU5Mzc1IC0yLjc5Njg3NSBDIDEuNzk2ODc1IC0zLjAzMTI1IDIuMDQ2ODc1IC0zLjE1NjI1IDIuMzU5Mzc1IC0zLjE1NjI1IEMgMi42ODc1IC0zLjE1NjI1IDIuOTM3NSAtMy4wMzEyNSAzLjE0MDYyNSAtMi43OTY4NzUgQyAzLjM0Mzc1IC0yLjU2MjUgMy40NTMxMjUgLTIuMjY1NjI1IDMuNDUzMTI1IC0xLjkyMTg3NSBDIDMuNDUzMTI1IC0xLjU0Njg3NSAzLjM0Mzc1IC0xLjI1IDMuMTQwNjI1IC0xLjAxNTYyNSBDIDIuOTM3NSAtMC43OTY4NzUgMi42NzE4NzUgLTAuNjcxODc1IDIuMzQzNzUgLTAuNjcxODc1IEMgMi4wMzEyNSAtMC42NzE4NzUgMS43ODEyNSAtMC43OTY4NzUgMS41NzgxMjUgLTEuMDMxMjUgQyAxLjM3NSAtMS4yNjU2MjUgMS4yODEyNSAtMS41NjI1IDEuMjgxMjUgLTEuOTM3NSBDIDEuMjgxMjUgLTIuMjgxMjUgMS4zOTA2MjUgLTIuNTYyNSAxLjU5Mzc1IC0yLjc5Njg3NSBaIE0gMS41OTM3NSAtMi43OTY4NzUgIgogICAgICAgICAgIHN0eWxlPSJzdHJva2U6bm9uZTsiIC8+CiAgICAgIDwvc3ltYm9sPgogICAgICA8c3ltYm9sCiAgICAgICAgIGlkPSJnbHlwaDAtNyIKICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiPgogICAgICAgIDxwYXRoCiAgICAgICAgICAgaWQ9InBhdGgyMyIKICAgICAgICAgICBkPSJNIDIuMTcxODc1IC0zLjAxNTYyNSBMIDIuMTcxODc1IC0zLjg0Mzc1IEwgMS40ODQzNzUgLTMuODQzNzUgTCAxLjQ4NDM3NSAtNS4yNSBMIDAuNTkzNzUgLTUuMjUgTCAwLjU5Mzc1IC0zLjg0Mzc1IEwgMC4yMDMxMjUgLTMuODQzNzUgTCAwLjIwMzEyNSAtMy4wMTU2MjUgTCAwLjU5Mzc1IC0zLjAxNTYyNSBMIDAuNTkzNzUgMCBMIDEuNDg0Mzc1IDAgTCAxLjQ4NDM3NSAtMy4wMTU2MjUgWiBNIDIuMTcxODc1IC0zLjAxNTYyNSAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICAgIDxzeW1ib2wKICAgICAgICAgaWQ9ImdseXBoMC04IgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDI2IgogICAgICAgICAgIGQ9Ik0gMC41NjI1IDAgTCAxLjQ1MzEyNSAwIEwgMS40NTMxMjUgLTEuODU5Mzc1IEMgMS40NTMxMjUgLTIuMzI4MTI1IDEuNTE1NjI1IC0yLjY3MTg3NSAxLjYyNSAtMi44NTkzNzUgQyAxLjc1IC0zLjA0Njg3NSAxLjk2ODc1IC0zLjE1NjI1IDIuMjY1NjI1IC0zLjE1NjI1IEMgMi41MzEyNSAtMy4xNTYyNSAyLjcxODc1IC0zLjA3ODEyNSAyLjgxMjUgLTIuOTM3NSBDIDIuOTIxODc1IC0yLjc4MTI1IDIuOTg0Mzc1IC0yLjUzMTI1IDIuOTg0Mzc1IC0yLjE1NjI1IEwgMi45ODQzNzUgMCBMIDMuODc1IDAgTCAzLjg3NSAtMi4zNTkzNzUgQyAzLjg3NSAtMi45MDYyNSAzLjc2NTYyNSAtMy4yODEyNSAzLjU0Njg3NSAtMy41MzEyNSBDIDMuMjk2ODc1IC0zLjgxMjUgMi45NTMxMjUgLTMuOTUzMTI1IDIuNSAtMy45NTMxMjUgQyAyLjEwOTM3NSAtMy45NTMxMjUgMS43NjU2MjUgLTMuNzk2ODc1IDEuNDUzMTI1IC0zLjQ4NDM3NSBMIDEuNDUzMTI1IC0zLjg0Mzc1IEwgMC41NjI1IC0zLjg0Mzc1IFogTSAwLjU2MjUgMCAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICAgIDxzeW1ib2wKICAgICAgICAgaWQ9ImdseXBoMC05IgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDI5IgogICAgICAgICAgIGQ9IiIKICAgICAgICAgICBzdHlsZT0ic3Ryb2tlOm5vbmU7IiAvPgogICAgICA8L3N5bWJvbD4KICAgICAgPHN5bWJvbAogICAgICAgICBpZD0iZ2x5cGgwLTEwIgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDMyIgogICAgICAgICAgIGQ9Ik0gMy4zOTA2MjUgLTMuMzkwNjI1IEMgMy4wMzEyNSAtMy43NjU2MjUgMi42NDA2MjUgLTMuOTUzMTI1IDIuMTg3NSAtMy45NTMxMjUgQyAxLjY3MTg3NSAtMy45NTMxMjUgMS4yMzQzNzUgLTMuNzUgMC44OTA2MjUgLTMuMzc1IEMgMC41NDY4NzUgLTIuOTg0Mzc1IDAuMzc1IC0yLjUgMC4zNzUgLTEuOTM3NSBDIDAuMzc1IC0xLjM0Mzc1IDAuNTQ2ODc1IC0wLjg1OTM3NSAwLjg5MDYyNSAtMC40Njg3NSBDIDEuMjUgLTAuMDc4MTI1IDEuNjg3NSAwLjEwOTM3NSAyLjIwMzEyNSAwLjEwOTM3NSBDIDIuNjU2MjUgMC4xMDkzNzUgMy4wNDY4NzUgLTAuMDYyNSAzLjM5MDYyNSAtMC40MDYyNSBMIDMuMzkwNjI1IDAgTCA0LjI4MTI1IDAgTCA0LjI4MTI1IC02LjY3MTg3NSBMIDMuMzkwNjI1IC02LjY3MTg3NSBaIE0gMS41OTM3NSAtMi43OTY4NzUgQyAxLjc5Njg3NSAtMy4wMzEyNSAyLjA0Njg3NSAtMy4xNTYyNSAyLjM1OTM3NSAtMy4xNTYyNSBDIDIuNjg3NSAtMy4xNTYyNSAyLjkzNzUgLTMuMDMxMjUgMy4xNDA2MjUgLTIuNzk2ODc1IEMgMy4zNDM3NSAtMi41NjI1IDMuNDUzMTI1IC0yLjI2NTYyNSAzLjQ1MzEyNSAtMS45MjE4NzUgQyAzLjQ1MzEyNSAtMS41NDY4NzUgMy4zNDM3NSAtMS4yNSAzLjE0MDYyNSAtMS4wMTU2MjUgQyAyLjkzNzUgLTAuNzk2ODc1IDIuNjcxODc1IC0wLjY3MTg3NSAyLjM0Mzc1IC0wLjY3MTg3NSBDIDIuMDMxMjUgLTAuNjcxODc1IDEuNzgxMjUgLTAuNzk2ODc1IDEuNTc4MTI1IC0xLjAzMTI1IEMgMS4zNzUgLTEuMjY1NjI1IDEuMjgxMjUgLTEuNTYyNSAxLjI4MTI1IC0xLjkzNzUgQyAxLjI4MTI1IC0yLjI4MTI1IDEuMzkwNjI1IC0yLjU2MjUgMS41OTM3NSAtMi43OTY4NzUgWiBNIDEuNTkzNzUgLTIuNzk2ODc1ICIKICAgICAgICAgICBzdHlsZT0ic3Ryb2tlOm5vbmU7IiAvPgogICAgICA8L3N5bWJvbD4KICAgICAgPHN5bWJvbAogICAgICAgICBpZD0iZ2x5cGgwLTExIgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDM1IgogICAgICAgICAgIGQ9Ik0gNC4wNjI1IC0xLjkyMTg3NSBDIDQuMDYyNSAtMi41NDY4NzUgMy45MDYyNSAtMy4wNDY4NzUgMy41NzgxMjUgLTMuNDA2MjUgQyAzLjI1IC0zLjc2NTYyNSAyLjc5Njg3NSAtMy45NTMxMjUgMi4yMzQzNzUgLTMuOTUzMTI1IEMgMS42NzE4NzUgLTMuOTUzMTI1IDEuMjE4NzUgLTMuNzY1NjI1IDAuODc1IC0zLjM5MDYyNSBDIDAuNTQ2ODc1IC0zLjAxNTYyNSAwLjM3NSAtMi41MzEyNSAwLjM3NSAtMS45MjE4NzUgQyAwLjM3NSAtMS4yOTY4NzUgMC41NDY4NzUgLTAuODEyNSAwLjg5MDYyNSAtMC40Mzc1IEMgMS4yNSAtMC4wNzgxMjUgMS43MDMxMjUgMC4xMDkzNzUgMi4yODEyNSAwLjEwOTM3NSBDIDIuNDg0Mzc1IDAuMTA5Mzc1IDIuNjcxODc1IDAuMDkzNzUgMi44MjgxMjUgMC4wNDY4NzUgQyAzIDAuMDE1NjI1IDMuMTcxODc1IC0wLjA2MjUgMy4zMTI1IC0wLjE1NjI1IEMgMy40NTMxMjUgLTAuMjUgMy41OTM3NSAtMC4zNTkzNzUgMy43MTg3NSAtMC41IEMgMy44NTkzNzUgLTAuNjQwNjI1IDMuOTg0Mzc1IC0wLjgxMjUgNC4wOTM3NSAtMS4wMTU2MjUgTCAzLjM0Mzc1IC0xLjQyMTg3NSBDIDMuMTcxODc1IC0xLjE0MDYyNSAzIC0wLjkzNzUgMi44NTkzNzUgLTAuODQzNzUgQyAyLjcxODc1IC0wLjczNDM3NSAyLjUzMTI1IC0wLjY3MTg3NSAyLjMxMjUgLTAuNjcxODc1IEMgMi4wMzEyNSAtMC42NzE4NzUgMS43OTY4NzUgLTAuNzgxMjUgMS42MDkzNzUgLTAuOTY4NzUgQyAxLjQzNzUgLTEuMTQwNjI1IDEuMzI4MTI1IC0xLjM5MDYyNSAxLjMxMjUgLTEuNzE4NzUgTCA0LjA2MjUgLTEuNzE4NzUgWiBNIDEuMzU5Mzc1IC0yLjQzNzUgQyAxLjM5MDYyNSAtMi41NDY4NzUgMS40Mzc1IC0yLjY1NjI1IDEuNSAtMi43MzQzNzUgQyAxLjU0Njg3NSAtMi44MTI1IDEuNjA5Mzc1IC0yLjg5MDYyNSAxLjY4NzUgLTIuOTUzMTI1IEMgMS43NjU2MjUgLTMuMDE1NjI1IDEuODU5Mzc1IC0zLjA2MjUgMS45NTMxMjUgLTMuMTA5Mzc1IEMgMi4wNDY4NzUgLTMuMTQwNjI1IDIuMTQwNjI1IC0zLjE1NjI1IDIuMjUgLTMuMTU2MjUgQyAyLjcxODc1IC0zLjE1NjI1IDMuMDE1NjI1IC0yLjkwNjI1IDMuMTU2MjUgLTIuNDM3NSBaIE0gMS4zNTkzNzUgLTIuNDM3NSAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICAgIDxzeW1ib2wKICAgICAgICAgaWQ9ImdseXBoMC0xMiIKICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiPgogICAgICAgIDxwYXRoCiAgICAgICAgICAgaWQ9InBhdGgzOCIKICAgICAgICAgICBkPSJNIDAuMDMxMjUgLTYuMDkzNzUgTCAyLjcwMzEyNSAwLjQ1MzEyNSBMIDUuNDM3NSAtNi4wOTM3NSBMIDQuNDIxODc1IC02LjA5Mzc1IEwgMi43MTg3NSAtMS44NTkzNzUgTCAxLjAzMTI1IC02LjA5Mzc1IFogTSAwLjAzMTI1IC02LjA5Mzc1ICIKICAgICAgICAgICBzdHlsZT0ic3Ryb2tlOm5vbmU7IiAvPgogICAgICA8L3N5bWJvbD4KICAgICAgPHN5bWJvbAogICAgICAgICBpZD0iZ2x5cGgwLTEzIgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDQxIgogICAgICAgICAgIGQ9Ik0gMC41NjI1IC0zLjg0Mzc1IEwgMC41NjI1IC0xLjYwOTM3NSBDIDAuNTYyNSAtMS4yNjU2MjUgMC41OTM3NSAtMSAwLjY3MTg3NSAtMC44MjgxMjUgQyAwLjc1IC0wLjYyNSAwLjg3NSAtMC40Mzc1IDEuMDYyNSAtMC4yODEyNSBDIDEuMzU5Mzc1IC0wLjAxNTYyNSAxLjc1IDAuMTA5Mzc1IDIuMjAzMTI1IDAuMTA5Mzc1IEMgMi42NzE4NzUgMC4xMDkzNzUgMy4wNDY4NzUgLTAuMDE1NjI1IDMuMzQzNzUgLTAuMjgxMjUgQyAzLjUzMTI1IC0wLjQzNzUgMy42NTYyNSAtMC42MjUgMy43MTg3NSAtMC44MjgxMjUgQyAzLjgxMjUgLTEuMDQ2ODc1IDMuODQzNzUgLTEuMzEyNSAzLjg0Mzc1IC0xLjYwOTM3NSBMIDMuODQzNzUgLTMuODQzNzUgTCAyLjk1MzEyNSAtMy44NDM3NSBMIDIuOTUzMTI1IC0xLjY0MDYyNSBDIDIuOTUzMTI1IC0xIDIuNzAzMTI1IC0wLjY3MTg3NSAyLjIwMzEyNSAtMC42NzE4NzUgQyAxLjcwMzEyNSAtMC42NzE4NzUgMS40NTMxMjUgLTEgMS40NTMxMjUgLTEuNjQwNjI1IEwgMS40NTMxMjUgLTMuODQzNzUgWiBNIDAuNTYyNSAtMy44NDM3NSAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICAgIDxzeW1ib2wKICAgICAgICAgaWQ9ImdseXBoMC0xNCIKICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiPgogICAgICAgIDxwYXRoCiAgICAgICAgICAgaWQ9InBhdGg0NCIKICAgICAgICAgICBkPSJNIDAuNTYyNSAtNi42NzE4NzUgTCAwLjU2MjUgMCBMIDEuNDUzMTI1IDAgTCAxLjQ1MzEyNSAtNi42NzE4NzUgWiBNIDAuNTYyNSAtNi42NzE4NzUgIgogICAgICAgICAgIHN0eWxlPSJzdHJva2U6bm9uZTsiIC8+CiAgICAgIDwvc3ltYm9sPgogICAgICA8c3ltYm9sCiAgICAgICAgIGlkPSJnbHlwaDAtMTUiCiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIj4KICAgICAgICA8cGF0aAogICAgICAgICAgIGlkPSJwYXRoNDciCiAgICAgICAgICAgZD0iTSAwLjU2MjUgMCBMIDEuNDUzMTI1IDAgTCAxLjQ1MzEyNSAtMiBDIDEuNDUzMTI1IC0yLjcxODc1IDEuNjg3NSAtMy4wNzgxMjUgMi4xNzE4NzUgLTMuMDc4MTI1IEMgMi4zMjgxMjUgLTMuMDc4MTI1IDIuNSAtMy4wMzEyNSAyLjY3MTg3NSAtMi45MDYyNSBMIDMuMDYyNSAtMy43MTg3NSBDIDIuODEyNSAtMy44NzUgMi41NzgxMjUgLTMuOTUzMTI1IDIuMzQzNzUgLTMuOTUzMTI1IEMgMi4xNzE4NzUgLTMuOTUzMTI1IDIuMDE1NjI1IC0zLjkyMTg3NSAxLjg3NSAtMy44NTkzNzUgQyAxLjc1IC0zLjc4MTI1IDEuNjA5Mzc1IC0zLjY3MTg3NSAxLjQ1MzEyNSAtMy41IEwgMS40NTMxMjUgLTMuODQzNzUgTCAwLjU2MjUgLTMuODQzNzUgWiBNIDAuNTYyNSAwICIKICAgICAgICAgICBzdHlsZT0ic3Ryb2tlOm5vbmU7IiAvPgogICAgICA8L3N5bWJvbD4KICAgICAgPHN5bWJvbAogICAgICAgICBpZD0iZ2x5cGgwLTE2IgogICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSI+CiAgICAgICAgPHBhdGgKICAgICAgICAgICBpZD0icGF0aDUwIgogICAgICAgICAgIGQ9Ik0gMC40NTMxMjUgLTQuMDMxMjUgTCAxLjA2MjUgLTMuNzgxMjUgTCAyLjE4NzUgLTYuMDQ2ODc1IEwgMS4zOTA2MjUgLTYuMzU5Mzc1IFogTSAwLjQ1MzEyNSAtNC4wMzEyNSAiCiAgICAgICAgICAgc3R5bGU9InN0cm9rZTpub25lOyIgLz4KICAgICAgPC9zeW1ib2w+CiAgICA8L2c+CiAgICA8Y2xpcFBhdGgKICAgICAgIGlkPSJjbGlwMSI+CiAgICAgIDxwYXRoCiAgICAgICAgIGlkPSJwYXRoNTUiCiAgICAgICAgIGQ9Ik0gMTI4IDc1IEwgMTMyLjkyMTg3NSA3NSBMIDEzMi45MjE4NzUgODAgTCAxMjggODAgWiBNIDEyOCA3NSAiIC8+CiAgICA8L2NsaXBQYXRoPgogIDwvZGVmcz4KICA8ZwogICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgaWQ9ImcxMjIiPgogICAgPHVzZQogICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC05IgogICAgICAgeD0iNDEuNjAwNTIxIgogICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgaWQ9InVzZTEyMCIKICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogIDwvZz4KICA8ZwogICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgaWQ9ImcxMzQiPgogICAgPHVzZQogICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC05IgogICAgICAgeD0iNTYuNjgyNDUzIgogICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgaWQ9InVzZTEzMiIKICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogIDwvZz4KICA8ZwogICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgaWQ9ImcxNzgiPgogICAgPHVzZQogICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC05IgogICAgICAgeD0iOTguNTA4NDA4IgogICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgaWQ9InVzZTE3NiIKICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogIDwvZz4KICA8ZwogICAgIGlkPSJnNDg3NyI+CiAgICA8ZwogICAgICAgaWQ9Imc0NzM3Ij4KICAgICAgPHBhdGgKICAgICAgICAgc3R5bGU9ImZpbGw6I2QxY2ZjZjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSIKICAgICAgICAgZD0ibSA3My40ODgyODEsMTcuMTk5MjE5IDIuMzUxNTYzLDEuOTMzNTkzIGMgMCwwIC02LjQ2NDg0NCwxLjkxMDE1NyAtOC4yNTM5MDYsMi4zNjMyODIgbCA1LjkwMjM0MywtNC4yOTY4NzUiCiAgICAgICAgIGlkPSJwYXRoNjAiIC8+CiAgICAgIDxwYXRoCiAgICAgICAgIHN0eWxlPSJmaWxsOiNkMWNmY2Y7ZmlsbC1vcGFjaXR5OjE7ZmlsbC1ydWxlOm5vbnplcm87c3Ryb2tlOm5vbmUiCiAgICAgICAgIGQ9Im0gNzIuMzIwMzEyLDE2LjAzOTA2MiBjIDAsMCA1LjY1MjM0NCwtMTAuMjUgMTYuMDg1OTM4LC0xMS44NzEwOTMgMS4yNDYwOTQsLTAuMTkxNDA3IDAuMjAzMTI1LDIuMTEzMjgxIDAuMTkxNDA2LDMuMTY0MDYyIC0wLjAwMzksMC42MzI4MTMgMC43NDIxODgsMS4yMTA5MzggMC40NzI2NTYsMS4yNDYwOTQgLTUuODA0Njg3LDAuNzI2NTYzIC0xMi40NDE0MDYsMy40NzI2NTYgLTE2Ljc1LDcuNDYwOTM3IgogICAgICAgICBpZD0icGF0aDYyIiAvPgogICAgICA8cGF0aAogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lIgogICAgICAgICBkPSJtIDcyLjMyMDMxMiwxNi4wMzkwNjIgYyAwLDAgMi41LC00Ljk5MjE4NyA4LjM5MDYyNiwtOC44NDc2NTYgLTEuNDk2MDk0LDEuODg2NzE5IC0yLjQ4ODI4MiwzLjEzMjgxMyAtMi44OTQ1MzIsNS4wNDI5NjkgLTIuMDY2NDA2LDEuMTI1IC0zLjk2ODc1LDIuMzg2NzE5IC01LjQ5NjA5NCwzLjgwNDY4NyIKICAgICAgICAgaWQ9InBhdGg2NCIgLz4KICAgICAgPHBhdGgKICAgICAgICAgc3R5bGU9ImZpbGw6I2RlNTc2YjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSIKICAgICAgICAgZD0ibSA3OS4wODIwMzEsMjEuMjE4NzUgLTAuNjE3MTg3LC0wLjM5MDYyNSAtMi43ODEyNSwxLjIxNDg0NCAtMS4wODIwMzIsMC4zMjgxMjUgLTAuNjE3MTg3LDAuNjYwMTU2IC03LjIxMDkzNyw0LjMyNDIxOSAtMi4xMDkzNzYsLTAuMTA5Mzc1IDAuNzkyOTY5LDAuNTg1OTM3IC0yLjA3NDIxOSwwLjY0NDUzMSAtMC42OTkyMTgsMS4xNjAxNTcgMy44ODY3MTgsLTEuMDU4NTk0IDAuNzE4NzUsLTAuODAwNzgxIDcuMDg5ODQ0LC00LjE0MDYyNSAxLjA5NzY1NiwtMC4zNzEwOTQgMC41MDc4MTMsLTAuNTg1OTM3IDMuMDk3NjU2LC0xLjQ2MDkzOCIKICAgICAgICAgaWQ9InBhdGg2NiIgLz4KICAgICAgPHBhdGgKICAgICAgICAgc3R5bGU9ImZpbGw6I2RlNTc2YjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSIKICAgICAgICAgZD0ibSA4MC40NjQ4NDQsMjIuMTI1IC0wLjYwOTM3NSwtMC4zNTkzNzUgLTIuOTg4MjgxLDEuNDUzMTI1IC0xLjE2NDA2MywwLjQwMjM0NCAtMC42NDg0MzcsMC43NDYwOTQgLTcuNjkxNDA3LDUuMDM5MDYyIC0yLjMxMjUsLTAuMDMxMjUgMC44OTA2MjUsMC42MDkzNzUgLTIuMjM4MjgxLDAuNzk2ODc1IC0wLjcxNDg0NCwxLjI5Mjk2OSA0LjIwMzEyNSwtMS4zMjQyMTkgMC43NSwtMC45MDYyNSA3LjU3MDMxMywtNC44MjgxMjUgMS4xODM1OTMsLTAuNDU3MDMxIDAuNTI3MzQ0LC0wLjY1NjI1IDMuMjQyMTg4LC0xLjc3NzM0NCIKICAgICAgICAgaWQ9InBhdGg2OCIgLz4KICAgICAgPHBhdGgKICAgICAgICAgc3R5bGU9ImZpbGw6I2QxY2ZjZjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSIKICAgICAgICAgZD0ibSA5NS42ODM1OTQsMTAuNTE5NTMxIGMgMCwwIDAuODI4MTI1LDIuMjkyOTY5IDEuNTYyNSwyLjM1NTQ2OSA0LjQyNTc4NiwwLjM3ODkwNiAxMC44MjQyMTYsLTQuNzg1MTU2IDExLjU4MjAzNiwtNS44NzUgMS40NTMxMiwtMi4wODU5MzggOC43MDcwMywtNyA4LjcwNzAzLC03IDAsMCAtNy4xNDg0NCwyLjc4MTI1IC04LjIwNzAzLDMuMzI4MTI1IC0xLjA1ODYsMC41NDY4NzUgLTIuNTE5NTQsMC4xNzE4NzUgLTMuMjUsMC45NjQ4NDQgLTAuNzY5NTQsMC44MzU5MzcgLTAuNjY3OTcsMS4zNzUgLTAuNjA1NDcsMi4xODc1IDAuMTY0MDYsMi4wOTc2NTYgLTUuODc1MDA0LDQuNDgwNDY5IC05Ljc4OTA2Niw0LjAzOTA2MiIKICAgICAgICAgaWQ9InBhdGg3MCIgLz4KICAgICAgPHBhdGgKICAgICAgICAgc3R5bGU9ImZpbGw6I2RlNTc2YjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSIKICAgICAgICAgZD0ibSAxMDguODI4MTMsNyBjIDEuMjIyNjUsLTEuODY3MTg4IDguNzA3MDMsLTcgOC43MDcwMywtNyAwLDAgLTcuMTQ4NDQsMi43ODEyNSAtOC4yMDcwMywzLjMyODEyNSAtMC4zNTE1NywwLjc5Njg3NSAtMC42NDg0NCwxLjU5NzY1NiAtMS4wMjM0NCwyLjQ0OTIxOSAwLjE3OTY5LDAuNDc2NTYyIDAuMzA0NjksMC42MzY3MTggMC41MjM0NCwxLjIyMjY1NiIKICAgICAgICAgaWQ9InBhdGg3MiIgLz4KICAgICAgPHBhdGgKICAgICAgICAgc3R5bGU9ImZpbGw6I2QxY2ZjZjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSIKICAgICAgICAgZD0ibSA5NC45NjA5MzgsMjIuMjc3MzQ0IGMgMCwwIC0wLjMwODU5NCwzLjMyMDMxMiAwLjMxNjQwNiw2LjE1MjM0NCAtMS4yNTc4MTMsLTAuNDE3OTY5IC0yLjQ5MjE4OCwtMC44Nzg5MDcgLTMuNzA3MDMyLC0xLjM3ODkwNyAtMC43MDMxMjQsLTEuMzgyODEyIC0xLjM0Mzc1LC0yLjkwMjM0MyAtMS42MzI4MTIsLTQuMjc3MzQzIDAsMCAtMC4xNTYyNSwxLjYyNSAtMC4wMjczNCwzLjU3MDMxMiAtOS4xMjEwOTQsLTQuMDI3MzQ0IC0xNi40NjQ4NDQsLTkuNTg1OTM4IC0xNi40NjQ4NDQsLTkuNTg1OTM4IDMuNjk5MjE5LC00LjEzNjcxOCAxMC40ODA0NjksLTYuODAwNzgxIDE1Ljg5ODQzOCwtNy41IDUuNDI1NzgxLC0wLjY5OTIxOCA1LjQ1NzAzMSwwLjc4MTI1IDYuNDcyNjU2LDIuNjM2NzE5IDcuMjMwNDcsMTMuMTU2MjUgMTYuNDg0MzcsMTcuNTY2NDA3IDE2LjQ4NDM3LDE3LjU2NjQwNyAtNC4yODEyNSwxLjQ4ODI4MSAtOS4yNSwxLjExNzE4NyAtMTQuMTcxODcsLTAuMTc5Njg4IC0xLjEyMTA5NCwtMS44MTY0MDYgLTIuNjYwMTU2LC00LjYxNzE4OCAtMy4xNjc5NjgsLTcuMDAzOTA2IgogICAgICAgICBpZD0icGF0aDc0IiAvPgogICAgICA8cGF0aAogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lIgogICAgICAgICBkPSJtIDk0Ljk2MDkzOCwyMi4yNzczNDQgYyAwLDAgLTAuMzA4NTk0LDMuMzIwMzEyIDAuMzE2NDA2LDYuMTUyMzQ0IC0xLjI1NzgxMywtMC40MTc5NjkgLTIuNDkyMTg4LC0wLjg3ODkwNyAtMy43MDcwMzIsLTEuMzc4OTA3IC0wLjcwMzEyNCwtMS4zODI4MTIgLTEuMzQzNzUsLTIuOTAyMzQzIC0xLjYzMjgxMiwtNC4yNzczNDMgMCwwIC0wLjE1NjI1LDEuNjI1IC0wLjAyNzM0LDMuNTcwMzEyIC05LjEyMTA5NCwtNC4wMjczNDQgLTE2LjQ2NDg0NCwtOS41ODU5MzggLTE2LjQ2NDg0NCwtOS41ODU5MzggMS4zOTQ1MzIsLTEuNTU4NTkzIDMuMjMwNDY5LC0yLjkwNjI1IDUuMjYxNzE5LC00LjAyMzQzNyAwLjI5Njg3NSwtMC4xNjQwNjMgNy45OTIxODgsMy4zMDQ2ODcgMTguNTM1MTU3LDEuNjA1NDY5IDcuMTgzNTg4LDExLjMwNDY4NyAxNS4wNTg1ODgsMTUuMTIxMDk0IDE1LjA1ODU4OCwxNS4xMjEwOTQgLTQuMjgxMjUsMS40ODgyODEgLTkuMjUsMS4xMTcxODcgLTE0LjE3MTg3LC0wLjE3OTY4OCAtMS4xMjEwOTQsLTEuODE2NDA2IC0yLjY2MDE1NiwtNC42MTcxODggLTMuMTY3OTY4LC03LjAwMzkwNiIKICAgICAgICAgaWQ9InBhdGg3NiIgLz4KICAgIDwvZz4KICAgIDxnCiAgICAgICBpZD0iZzQ4MDIiPgogICAgICA8cGF0aAogICAgICAgICBzdHlsZT0iZmlsbDojYjljZjYyO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lIgogICAgICAgICBkPSJtIDQwLjE3OTY4OCw1My4wMTk1MzEgLTUuMDMxMjUsLTExLjU0Njg3NSAtNS4yODEyNSwxMS41NDY4NzUgeiBtIDIuMDg5ODQzLDQuODE2NDA3IEggMjcuNzM0Mzc1IGwgLTMuNzg1MTU2LDguMjQ2MDkzIGggLTUuNTE5NTMxIGwgMTYuODA0Njg3LC0zNi4wOTM3NSAxNi4yMDcwMzEsMzYuMDkzNzUgaCAtNS42MDU0NjggbCAtMy41NjY0MDcsLTguMjQ2MDkzIgogICAgICAgICBpZD0icGF0aDc4IiAvPgogICAgICA8cGF0aAogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lIgogICAgICAgICBkPSJNIDQ3LjU2MjUsMTQuMTcxODc1IDY1LjkyOTY4OCw1NS42NTYyNSA3NS40NTMxMjUsMzIuMTIxMDk0IGggNS42MDU0NjkgTCA2NS44MjAzMTIsNjguNjMyODEyIDQxLjk1NzAzMSwxNC4xNzE4NzUgSCA0Ny41NjI1IgogICAgICAgICBpZD0icGF0aDgwIiAvPgogICAgICA8cGF0aAogICAgICAgICBzdHlsZT0iZmlsbDojYjljZjYyO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lIgogICAgICAgICBkPSJNIDEwMi4xMDE1Niw1My4wMTk1MzEgOTcuMDYyNSw0MS40NzI2NTYgOTEuNzg5MDYyLDUzLjAxOTUzMSBaIG0gMi4wODk4NSw0LjgxNjQwNyBIIDg5LjY1MjM0NCBsIC0zLjc4MTI1LDguMjQ2MDkzIGggLTUuNTIzNDM4IGwgMTYuODA0Njg4LC0zNi4wOTM3NSAxNi4yMTA5MzYsMzYuMDkzNzUgaCAtNS42MDkzNyBsIC0zLjU2MjUsLTguMjQ2MDkzIgogICAgICAgICBpZD0icGF0aDgyIiAvPgogICAgPC9nPgogICAgPGcKICAgICAgIGlkPSJnNDc5NyI+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnODYiPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTEiCiAgICAgICAgICAgeD0iMCIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTg0IgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzkwIj4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0yIgogICAgICAgICAgIHg9IjUuOTgzMzk5OSIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTg4IgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzk0Ij4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0yIgogICAgICAgICAgIHg9IjkuMjYwNzQwMyIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTkyIgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzEwMCI+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtMyIKICAgICAgICAgICB4PSIxMi41MzgwNzkiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2U5NiIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtNCIKICAgICAgICAgICB4PSIxNy4zOTI2OTgiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2U5OCIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgIDwvZz4KICAgICAgPGcKICAgICAgICAgc3R5bGU9ImZpbGw6IzIzNDIzZjtmaWxsLW9wYWNpdHk6MSIKICAgICAgICAgaWQ9ImcxMDYiPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTUiCiAgICAgICAgICAgeD0iMjEuMjQyMzA4IgogICAgICAgICAgIHk9Ijc5LjA5OTEzNiIKICAgICAgICAgICBpZD0idXNlMTAyIgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC02IgogICAgICAgICAgIHg9IjIzLjI1MzYyMiIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTEwNCIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgIDwvZz4KICAgICAgPGcKICAgICAgICAgc3R5bGU9ImZpbGw6IzIzNDIzZjtmaWxsLW9wYWNpdHk6MSIKICAgICAgICAgaWQ9ImcxMTAiPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTciCiAgICAgICAgICAgeD0iMjguMDk2ODg0IgogICAgICAgICAgIHk9Ijc5LjA5OTEzNiIKICAgICAgICAgICBpZD0idXNlMTA4IgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzExOCI+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtNSIKICAgICAgICAgICB4PSIzMC4zMDUzODQiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxMTIiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTMiCiAgICAgICAgICAgeD0iMzIuMzE2NyIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTExNCIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtOCIKICAgICAgICAgICB4PSIzNy4xNzEzMTgiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxMTYiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTMwIj4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0xMCIKICAgICAgICAgICB4PSI0NC4wOTMwMDIiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxMjQiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTExIgogICAgICAgICAgIHg9IjQ4LjkzOTU0NSIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTEyNiIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtMiIKICAgICAgICAgICB4PSI1My40MDY0NDEiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxMjgiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTM4Ij4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0xMiIKICAgICAgICAgICB4PSI1OS4xNzUxMzMiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxMzYiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTQ0Ij4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC01IgogICAgICAgICAgIHg9IjY0LjYzNzUyNyIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTE0MCIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtNyIKICAgICAgICAgICB4PSI2Ni42NDg4NDIiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxNDIiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTUwIj4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC01IgogICAgICAgICAgIHg9IjY4Ljg1NzM0NiIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTE0NiIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtNCIKICAgICAgICAgICB4PSI3MC44Njg2NiIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTE0OCIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgIDwvZz4KICAgICAgPGcKICAgICAgICAgc3R5bGU9ImZpbGw6IzIzNDIzZjtmaWxsLW9wYWNpdHk6MSIKICAgICAgICAgaWQ9ImcxNTQiPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTEzIgogICAgICAgICAgIHg9Ijc0LjcxODI2MiIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTE1MiIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgIDwvZz4KICAgICAgPGcKICAgICAgICAgc3R5bGU9ImZpbGw6IzIzNDIzZjtmaWxsLW9wYWNpdHk6MSIKICAgICAgICAgaWQ9ImcxNjAiPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTE0IgogICAgICAgICAgIHg9Ijc5LjExOTYzNyIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTE1NiIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtNyIKICAgICAgICAgICB4PSI4MS4xMzA5NTEiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxNTgiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTY0Ij4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0xMSIKICAgICAgICAgICB4PSI4My4zMzk5MjgiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxNjIiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTY4Ij4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0xMyIKICAgICAgICAgICB4PSI4Ny44MDg2ODUiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxNjYiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTc0Ij4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0xNSIKICAgICAgICAgICB4PSI5Mi4yMTAwNTIiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxNzAiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTIiCiAgICAgICAgICAgeD0iOTUuMjMxMDY0IgogICAgICAgICAgIHk9Ijc5LjA5OTEzNiIKICAgICAgICAgICBpZD0idXNlMTcyIgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzE4MiI+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtMTAiCiAgICAgICAgICAgeD0iMTAxLjAwMTU2IgogICAgICAgICAgIHk9Ijc5LjA5OTEzNiIKICAgICAgICAgICBpZD0idXNlMTgwIgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzE4NiI+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtMTYiCiAgICAgICAgICAgeD0iMTA1Ljg0NTMxIgogICAgICAgICAgIHk9Ijc5LjA5OTEzNiIKICAgICAgICAgICBpZD0idXNlMTg0IgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBzdHlsZT0iZmlsbDojMjM0MjNmO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzE5MCI+CiAgICAgICAgPHVzZQogICAgICAgICAgIHhsaW5rOmhyZWY9IiNnbHlwaDAtMSIKICAgICAgICAgICB4PSIxMDguNDgzOTgiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxODgiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMTk2Ij4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0xNCIKICAgICAgICAgICB4PSIxMTQuNDY3MzgiCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxOTIiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTIiCiAgICAgICAgICAgeD0iMTE2LjQ3ODciCiAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgIGlkPSJ1c2UxOTQiCiAgICAgICAgICAgd2lkdGg9IjEwMCUiCiAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICA8L2c+CiAgICAgIDxnCiAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgIGlkPSJnMjAwIj4KICAgICAgICA8dXNlCiAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC02IgogICAgICAgICAgIHg9IjExOS43NTYwNCIKICAgICAgICAgICB5PSI3OS4wOTkxMzYiCiAgICAgICAgICAgaWQ9InVzZTE5OCIKICAgICAgICAgICB3aWR0aD0iMTAwJSIKICAgICAgICAgICBoZWlnaHQ9IjEwMCUiIC8+CiAgICAgIDwvZz4KICAgICAgPGcKICAgICAgICAgc3R5bGU9ImZpbGw6IzIzNDIzZjtmaWxsLW9wYWNpdHk6MSIKICAgICAgICAgaWQ9ImcyMDQiPgogICAgICAgIDx1c2UKICAgICAgICAgICB4bGluazpocmVmPSIjZ2x5cGgwLTQiCiAgICAgICAgICAgeD0iMTI0LjU5OTc5IgogICAgICAgICAgIHk9Ijc5LjA5OTEzNiIKICAgICAgICAgICBpZD0idXNlMjAyIgogICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgIGhlaWdodD0iMTAwJSIgLz4KICAgICAgPC9nPgogICAgICA8ZwogICAgICAgICBjbGlwLXBhdGg9InVybCgjY2xpcDEpIgogICAgICAgICBpZD0iZzIxMCIKICAgICAgICAgc3R5bGU9ImNsaXAtcnVsZTpub256ZXJvIj4KICAgICAgICA8ZwogICAgICAgICAgIHN0eWxlPSJmaWxsOiMyMzQyM2Y7ZmlsbC1vcGFjaXR5OjEiCiAgICAgICAgICAgaWQ9ImcyMDgiPgogICAgICAgICAgPHVzZQogICAgICAgICAgICAgeGxpbms6aHJlZj0iI2dseXBoMC0xMSIKICAgICAgICAgICAgIHg9IjEyOC40NDk0IgogICAgICAgICAgICAgeT0iNzkuMDk5MTM2IgogICAgICAgICAgICAgaWQ9InVzZTIwNiIKICAgICAgICAgICAgIHdpZHRoPSIxMDAlIgogICAgICAgICAgICAgaGVpZ2h0PSIxMDAlIiAvPgogICAgICAgIDwvZz4KICAgICAgPC9nPgogICAgPC9nPgogIDwvZz4KPC9zdmc+Cg==" style="height:28px;" alt="" /></a>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="padding-left: 0;">
        <?php if($sf_user->isAdmin()): ?>
        <ul class="nav navbar-nav <?php if($compte): ?>mode-operateur<?php endif; ?>" style="border: 0;">
            <li id="nav_item_operateur" class="<?php if(!$compte): ?>disabled<?php endif; ?>"><a onclick="document.location = $(this).parents('ul.mode-operateur').find('li.active a').attr('href');" href="#"><span class="glyphicon glyphicon-user"></span></a></li>
            <li class="<?php if($route instanceof InterfaceDeclarationRoute): ?>active<?php endif; ?>"><a href="<?php if($etablissement && !$route instanceof InterfaceDeclarationRoute): ?><?php echo url_for('declaration_etablissement', $etablissement); ?><?php else: ?><?php echo url_for('declaration'); ?><?php endif; ?>">Déclarations</a></li>
            <li class="<?php if($route instanceof InterfaceFacturationRoute): ?>active<?php endif; ?>"><a href="<?php if($compte  && !$route instanceof InterfaceFacturationRoute): ?><?php echo url_for('facturation_declarant', $compte); ?><?php else: ?><?php echo url_for('facturation'); ?><?php endif; ?>">Facturation</a></li>
            <li class="<?php if($route instanceof InterfaceDegustationGeneralRoute): ?>active<?php endif; ?>"><a href="<?php if($etablissement && !$route instanceof InterfaceDegustationGeneralRoute): ?><?php echo url_for('degustation_declarant', $etablissement); ?><?php else: ?><?php echo url_for('degustation'); ?><?php endif; ?>">Dégustation</a></li>
            <li class="<?php if($route instanceof InterfaceConstatsRoute): ?>active<?php endif; ?>"><a href="<?php if($compte && !$route instanceof InterfaceConstatsRoute): ?><?php echo url_for('rendezvous_declarant', $compte); ?><?php else: ?><?php echo url_for('constats',array('jour' => date('Y-m-d'))); ?><?php endif; ?>">Constats</a></li>
            <li class="<?php if($route instanceof InterfaceContactsRoute): ?>active<?php endif; ?>"><a href="<?php if($compte && !$route instanceof InterfaceContactsRoute): ?><?php echo url_for('compte_visualisation', $compte); ?><?php else: ?><?php echo url_for('compte_recherche'); ?><?php endif; ?>">Contacts</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="<?php if($route instanceof InterfaceExportRoute): ?>active<?php endif; ?>"><a href="<?php echo url_for('export'); ?>"><span class="glyphicon glyphicon-export"></span> Export</a></li>
        </ul>
    <?php else: ?>
            <ul class="nav navbar-nav <?php if($compte): ?>mode-operateur<?php endif; ?>" style="border: 0;">
                <li class="<?php if($route instanceof InterfaceDeclarationRoute): ?>active<?php endif; ?>"><a href="<?php echo url_for('declaration_etablissement', $etablissement); ?>">Déclarations</a></li>
                <li class="<?php if($route instanceof InterfaceFacturationRoute): ?>active<?php endif; ?>"><a href="<?php echo url_for('facturation_declarant', $compte); ?>">Facturation</a></li>
            </ul>
        <?php endif; ?>
    </div>
</nav>
