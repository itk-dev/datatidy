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

        When I fill in "Name" with "Data flow with replace values"
        And select "ODAA c65b055d-a020-4871-ab51-bdbc3fd73fd8" from "Data source"
        And I press "Create data flow"
        Then I should see "Data flow Data flow with replace values created"

        When I follow "Edit recipe"
        Then the url should match "/data/flow/[^/]+/transforms/"
        And I should see "Add new step"

        When I select "Replace values" from "Add new step"
        And press "Add transform"
        Then the url should match "/data/flow/[^/]+/transforms/new"

        When I fill in "Name" with "Replace partial value (9 → nine)"
        # Select columns
        And I check "value"
        And I fill in "From" with "9"
        And I fill in "To" with "nine"
        And I check "Partial"
        And press "Save step"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+"

        When I follow "Edit"
        Then the url should match "/data/flow/[^/]+/transforms/[^/]+/edit"
        And the "From" field should contain "9"
        And the "To" field should contain "nine"
        And the "Partial" checkbox should be checked
