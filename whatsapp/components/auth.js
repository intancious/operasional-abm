const router = require("express").Router();
const fs = require("fs");

router.get("/checkauth", async (req, res) => {
    client
        .getState()
        .then((data) => {
            console.log(data);
            res.send(data);
        })
        .catch((err) => {
            if (err) {
                res.send("DISCONNECTED");
            }
        });
});

router.get("/getqr", async (req, res) => {
    client
        .getState()
        .then((data) => {
            if (data) {
                res.send({ status: true, authenticated: true, message: `Already Authenticated` })
                res.end();
            } else sendQr(res);
        })
        .catch(() => sendQr(res));
});

router.get("/restart", async (req, res) => {
    console.log("restarting ...");
    res.send({ status: true, message: `Restarting Service ...` });
    res.end();
});

function sendQr(res) {
    fs.readFile("components/last.qr", (err, last_qr) => {
        if (!err && last_qr) {
            res.send({ status: true, authenticated: false, message: `Please scan barcode`, qrcode: last_qr.toString() })
            res.end();
        }
    });
}

module.exports = router;