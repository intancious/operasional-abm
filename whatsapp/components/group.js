const router = require('express').Router();
const { MessageMedia, Location } = require("whatsapp-web.js");
const axios = require('axios');

const findGroupByName = async function (name) {
    const group = await client.getChats().then(chats => {
        return chats.find(chat =>
            chat.isGroup && chat.name.toLowerCase() == name.toLowerCase()
        );
    });
    return group;
}

router.post('/sendmessage/:chatname', async (req, res) => {
    let chatname = req.params.chatname;
    let message = req.body.message;

    let groupname = chatname.replace(/[+]/g, ' ');
    const group = await findGroupByName(groupname);
    if (!group) {
        res.send({ status: false, message: `Group not found` });
        res.end();
    }

    if (!group || message == undefined) {
        res.send({ status: false, message: "Please enter valid group name and message" })
    } else {
        client.sendMessage(group.id._serialized, message).then((response) => {
            res.send({ status: true, message: `Message successfully send to ${groupname}` })
        });
    }
});

router.post('/sendmedia/:chatname', async (req, res) => {
    let chatname = req.params.chatname;
    let fileUrl = req.body.file;
    let caption = req.body.caption;

    let groupname = chatname.replace(/[+]/g, ' ');
    const group = await findGroupByName(groupname);
    if (!group) {
        res.send({ status: false, message: `Group not found` });
        res.end();
    }

    if (!group || fileUrl == undefined) {
        res.send({ status: false, message: "Please enter valid group name and base64/url of image" })
    } else {
        let mimetype;
        const attachment = await axios.get(fileUrl, {
            responseType: 'arraybuffer'
        }).then(response => {
            mimetype = response.headers['content-type'];
            return response.data.toString('base64');
        });
        const media = new MessageMedia(mimetype, attachment, 'Media');

        client.sendMessage(group.id._serialized, media, { caption: caption || "" }).then((response) => {
            res.send({ status: true, message: `Message successfully send to ${groupname}` })
        });
    }
});

router.post('/sendlocation/:chatname', async (req, res) => {
    let chatname = req.params.chatname;
    let latitude = req.body.latitude;
    let longitude = req.body.longitude;
    let desc = req.body.description;

    let groupname = chatname.replace(/[+]/g, ' ');
    const group = await findGroupByName(groupname);
    if (!group) {
        res.send({ status: false, message: `Group not found` });
        res.end();
    }

    if (!group || latitude == undefined || longitude == undefined) {
        res.send({ status: false, message: "please enter valid phone, latitude and longitude" })
    } else {
        let loc = new Location(latitude, longitude, desc || "");
        client.sendMessage(group.id._serialized, loc).then((response) => {
            res.send({ status: true, message: `Message successfully send to ${groupname}` })
        });
    }
});

module.exports = router;
