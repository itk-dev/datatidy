Feature:
    In order to prove that a user can create a data flow
    As a authenticated user
    I want to be able to create a data flow

    @javascript
    Scenario: Authenticate and create data flow
        When I go to "/"
        Then I should be on "/login"

        When I fill in the following:
            | Username | test@example.com |
            | password | test-password    |
        And I press "Log in"
        Then I should be on "/dashboard"

        When I go to "/data/flow"
        And I follow "New data flow"
        Then I am on "/data/flow/new"

        When I fill in "Name" with "Data flow created from test b3eeb0ff-c8a8-4824-99d6-e0a3747c8b0d"
        And select "ODAA b3eeb0ff-c8a8-4824-99d6-e0a3747c8b0d" from "Data source"
        And I press "Create data flow"
        Then I should see "Data flow Data flow created from test b3eeb0ff-c8a8-4824-99d6-e0a3747c8b0d created"

        When I follow "Edit recipe"
        Then the url should match "/data/flow/[^/]+/transforms/"
        And I should see "Add new step"

        When I select "Select columns" from "Add new step"
        And press "Add transform"
        And I fill in "Name" with "Exclude 2 columns"
        And check "_id"
        And check "REPORT_ID"
        And uncheck "Include"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"

        When I select "Select columns" from "Add new step"
        And press "Add transform"
        And I fill in "Name" with "Select 2 columns"
        And check "TIMESTAMP"
        And check "vehicleCount"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"

        When I select "Change type" from "Add new step"
        And press "Add transform"
        And I fill in "Name" with "Change type"
        And check "TIMESTAMP"
        And select "datetime" from "Type"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"

        When I select "Rename columns" from "Add new step"
        And press "Add transform"
        And I fill in "Name" with "Rename columns"
        And I press the 1st "Add another mapping" button
        And I fill in the 1st "From" with "TIMESTAMP"
        And I fill in the 1st "To" with "t"
        And I press the 1st "Add another mapping" button
        And I fill in the 2nd "From" with "vehicleCount"
        And I fill in the 2nd "To" with "count"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"
        And I save a screenshot in "renamed.png"

        When I select "Filter" from "Add new step"
        And press "Add transform"
        And I fill in "Name" with "Filter"
        And I fill in "Column" with "count"
        And I fill in "Match" with "11"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"
        And I save a screenshot in "final.png"
