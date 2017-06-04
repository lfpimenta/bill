# Call Billing

We deal with a lot of calls. Once a User has signed up for Talkdesk and have created their own account, they will then buy a new phone number. Using this number, a User is able to receive phone calls directly in their browser and optionally define a cellphone or office number which we'll use to forward the inbound call to. Users are also able to make outbound calls from their browser.

Once an inbound/outbound call has finished, we need to charge the User's account depending on the duration. This is done by deducting the cost of the call from an Account's credits (which uses Dollars as the currency). The amount we charge depends on our service provider, Twilio, so we use Twilio's prices plus a little margin.

For this challenge we'll just focus on the billing of inbound calls.

## The Problem

There are various events that occur during the lifetime of a call. When the `call_finished` event is triggered we need to bill an Account's credits based on:

* The call's duration
* The type of the Talkdesk number that received the call
* If the call was forwarded to a User's external number, then the price of the call to the specified country needs to be considered

The price we charge must be [Twilio's price](https://www.twilio.com/voice/pricing) plus a little margin, like 5 cents (See [Formula section](#formula)). You can find a `.csv` file with the prices [here](/problems/assets/call%20billing/Twilio%20-%20Voice%20Prices.csv).

In some cases, we might like to change the margin depending on our Client (for example, reduce the margin if they use a lot of minutes per month).

Another important aspect of billing for calls is keeping a record of the charge made to a User's Account, as we would like to display the list of calls they've made including the amount we've charged for each one.

When a call finishes, the information that's available when deciding how much to bill is:

* The duration of the call (in seconds)
* The Talkdesk Telephone's number that received the inbound call
* The external phone number that was used to forward the call (only defined when the User answers a call on their cellphone or office landline, otherwise it means they answered on their web browser)
* The corresponding Talkdesk account

### Objectives

Build a command line app that can remove credits from a given account:

    $ call_billing charge <call_duration> <account_name> <talkdesk_phone_number> <customer_phone_number> <forwarded_phone_number (optional)>

And that can list the charges for the given account:

    $ call_billing list <account_name>

### Formula

To calculate the price per minute for inbounds is as follows: `talkdesk_number_cost + external_number_cost + profit_margin`

The `talkdesk_number_cost` should be set to 1c except for two cases: US and UK Toll free numbers which should be set to 3c and 6c, respectively.

The `external_number_cost` should be set to 1c if the call is answered in the web browser, otherwise the price to charge should be the same as Twilio charges for calls to that number.

#### Additional Notes

- If you're having fun and want to take this a step further, why not build a web based system to do the same as the above? Or maybe you'd prefer just having the web-based system, that's cool as well
- At Talkdesk we use usually use MongoDB and Redis, but feel free to use another data store or even a simple file store

### Background Information

The way this is actually done in Talkdesk is by using RabbitMQ PubSub capaibilites to emit events and have the appropriate system consume those events. A sample of events that you'll see pass through are:

* **call_initiated** - When a Customer starts a call to a Talkdesk, before it actually starts to ring
* **call_answered** - When an Agent picks up a call
* **call_missed** - When a call wasn't answered by an Agent
* **call_finished** - When an answered call finishes, either by the Agent closing the call or the Customer hanging up
* **call_voicemail** - When a call resulted in a voicemail

This problem is basically only dealing with a **call_finished** event.

Here's a small example of the data the event has (they come in JSON):

```json
{
  "event":"call_finished",
  "type":"in",
  "duration":"91",
  "account_id":"4f4a37a201c642014200000c",
  "call_id":"9d036a18-0986-11e2-b2c6-3d435d81b7fd",
  "talkdesk_phone_number":"+14845348611",
  "customer_phone_number":"+351961918192",
  "forwarded_phone_number":null,
  "timestamp":"2012-09-28T16:09:07Z"
}
```

*Final note*: If there's anything you don't understand or is ambiguous, open an issue in your repository with the question ;)

---

[Go back to the Problems README](README.md)
