const router = require('express').Router();
const { MessageMedia, Location } = require("whatsapp-web.js");
const { phoneNumberFormatter } = require('./formatter');
const fs = require('fs');
const axios = require('axios');

const checkRegisteredNumber = async function (number) {
    const isRegistered = await client.isRegisteredUser(number + '@c.us');
    return isRegistered;
}

router.post('/sendmessage/:phone', async (req, res) => {
    let phone = phoneNumberFormatter(req.params.phone);
    let message = req.body.message;

    const isRegisteredNumber = await checkRegisteredNumber(phone);
    if (!isRegisteredNumber) {
        res.send({ status: false, message: `The number is not registered` })
        res.end();
    }

    if (phone == undefined || message == undefined) {
        res.send({ status: false, message: "please enter valid phone and message" })
    } else {
        client.sendMessage(phone + '@c.us', message).then((response) => {
            if (response.id.fromMe) {
                res.send({ status: true, message: `Message successfully sent to ${phone}` })
            }
        });
    }
});

router.post('/sendmedia/:phone', async (req, res) => {
    let phone = phoneNumberFormatter(req.params.phone);
    let fileUrl = req.body.file;
    let caption = req.body.caption;

    const isRegisteredNumber = await checkRegisteredNumber(phone);
    if (!isRegisteredNumber) {
        res.send({ status: false, message: `The number is not registered` })
        res.end();
    }

    if (phone == undefined || fileUrl == undefined) {
        res.send({ status: false, message: "please enter valid phone and base64/url of image" })
    } else {
        let mimetype;
        const attachment = await axios.get(fileUrl, {
            responseType: 'arraybuffer'
        }).then(response => {
            mimetype = response.headers['content-type'];
            return response.data.toString('base64');
        });

        const media = new MessageMedia(mimetype, attachment, 'Media');
        client.sendMessage(`${phone}@c.us`, media, { caption: caption || '' }).then((response) => {
            if (response.id.fromMe) {
                res.send({ status: true, message: `MediaMessage successfully sent to ${phone}` })
                fs.unlinkSync(path)
            }
        });
    }
});

router.post('/sendlocation/:phone', async (req, res) => {
    let phone = phoneNumberFormatter(req.params.phone);
    let latitude = req.body.latitude;
    let longitude = req.body.longitude;
    let desc = req.body.description;

    const isRegisteredNumber = await checkRegisteredNumber(phone);
    if (!isRegisteredNumber) {
        res.send({ status: false, message: `The number is not registered` })
        res.end();
    }

    if (phone == undefined || latitude == undefined || longitude == undefined) {
        res.send({ status: false, message: "please enter valid phone, latitude and longitude" })
    } else {
        let loc = new Location(latitude, longitude, desc || "");
        client.sendMessage(`${phone}@c.us`, loc).then((response) => {
            if (response.id.fromMe) {
                res.send({ status: true, message: `MediaMessage successfully sent to ${phone}` })
            }
        });
    }
});

router.get('/getchatbyid/:phone', async (req, res) => {
    let phone = phoneNumberFormatter(req.params.phone);

    const isRegisteredNumber = await checkRegisteredNumber(phone);
    if (!isRegisteredNumber) {
        res.send({ status: false, message: `The number is not registered` })
        res.end();
    }

    if (phone == undefined) {
        res.send({ status: false, message: "please enter valid phone number" });
    } else {
        client.getChatById(`${phone}@c.us`).then((chat) => {
            res.send({ status: true, message: chat });
        }).catch(() => {
            console.error("getchaterror")
            res.send({ status: false, message: "getchaterror" })
        })
    }
});

router.get('/getchats', async (req, res) => {
    client.getChats().then((chats) => {
        res.send({ status: true, message: chats });
    }).catch(() => {
        res.send({ status: false, message: "getchatserror" })
    })
});

module.exports = router;