const router = require('express').Router();
const { phoneNumberFormatter } = require('./formatter');

const checkRegisteredNumber = async function (number) {
    const isRegistered = await client.isRegisteredUser(number + '@c.us');
    return isRegistered;
}

router.get('/getcontacts', (req, res) => {
    client.getContacts().then((contacts) => {
        res.send(JSON.stringify(contacts));
    });
});

router.get('/getcontact/:phone', async (req, res) => {
    let phone = phoneNumberFormatter(req.params.phone);

    const isRegisteredNumber = await checkRegisteredNumber(phone);
    if (!isRegisteredNumber) {
        res.send({ status: false, message: `The number is not registered` })
        res.end();
    }

    if (phone != undefined) {
        client.getContactById(`${phone}@c.us`).then((contact) => {
            res.send(JSON.stringify(contact));
        }).catch((err) => {
            res.send({ status: false, message: 'Not found' });
        });
    }
});

router.get('/getprofilepic/:phone', async (req, res) => {
    let phone = phoneNumberFormatter(req.params.phone);

    const isRegisteredNumber = await checkRegisteredNumber(phone);
    if (!isRegisteredNumber) {
        res.send({ status: false, message: `The number is not registered` })
        res.end();
    }

    if (phone != undefined) {
        client.getProfilePicUrl(`${phone}@c.us`).then((imgurl) => {
            if (imgurl) {
                res.send({ status: true, message: imgurl });
            } else {
                res.send({ status: false, message: 'Not Found' });
            }
        })
    }
});

router.get('/isregistereduser/:phone', async (req, res) => {
    let phone = phoneNumberFormatter(req.params.phone);

    const isRegisteredNumber = await checkRegisteredNumber(phone);
    if (!isRegisteredNumber) {
        res.send({ status: false, message: `The number is not registered` })
        res.end();
    }
    
    if (phone != undefined) {
        client.isRegisteredUser(`${phone}@c.us`).then((is) => {

            is ? res.send({ status: true, message: `${phone} is a whatsapp user` })
                : res.send({ status: false, message: `${phone} is not a whatsapp user` });
        })
    } else {
        res.send({ status: false, message: 'Invalid Phone number' });
    }
});

module.exports = router;