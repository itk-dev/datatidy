Feature:
    In order to prove that a user can create a data flow
    As a authenticated user
    I want to be able to create a data flow

    Scenario: Authenticate and create data flow
        When I go to "/"
        Then I should be on "/login"

        When I fill in the following:
            | username | test@example.com |
            | password | test-password    |
        And I press "Log in"
        Then I should be on "/dashboard"

        When I go to "/data/flow"
        And I follow "New data flow"
        Then I am on "/data/flow/new"

        When I fill in "Name" with "Data flow created from test"
        And select "ODAA" from "Data source"
        And I press "Create data flow"
        Then I should see "Data flow Data flow created from test created"

        When I follow "Edit recipe"
        Then the url should match "/data/flow/[^/]+/transforms/"
        And I should see "Add new step"

        # And I choose "Select columns" from "Add new step"
        When I select "Select columns" from "Add new step"
        # And I select "App\DataTransformer\SelectColumnsDataTransformer" from "Add new step"
        And press "Add transform"
        Then the url should match "/data/flow/[^/]+/transforms/new"

        When I fill in "Name" with "Select 3 columns"
        And check "_id"
        And check "REPORT_ID"
        And check "status"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"

        When I follow "Edit"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+/edit"

        When I uncheck "REPORT_ID"
        And fill in "Name" with "Select 2 columns"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"
        And I should not see "Must be an array: columns"
