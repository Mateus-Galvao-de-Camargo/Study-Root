const { app, BrowserWindow } = require('electron')

let janelaPrincipal

app.on('ready', () => {
    janelaPrincipal = new BrowserWindow({
        height: 900,
        width: 1200,
        resizable: false
    })

    janelaPrincipal.loadURL(`file://${__dirname}/src/index.html`)
})