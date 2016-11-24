// Instance the tour
var tour = new Tour({
  steps: [
  {
    element: "#brand",
    title: "Welcome to this tour!",
    content: "Yu can close it if you don't want it, either way, this will tell you steps needed to used this website"
  },
  {
    element: "#create-story-list",
    title: "1. Create Story",
    content: "this is the first things to do, after you klik this button, all you need to do is copas the green tag list from any platform, paste it, and store it"
  },
  {
    element: "#create-child-tag-rev",
    title: "2. Create Child Tag Rev",
    content: "after get the green tagsthat will be deployed, you need to select the green tag and give description about it (canary time, automata test, etc)"
  },
  {
    element: "#create-daily-mail",
    title: "3. Create Daily Mail",
    content: "this is last step, just check the child tag rev that you've been made in step 2, and click send mail"
  },
]});

// Initialize the tour
tour.init();

// Start the tour
tour.start();
