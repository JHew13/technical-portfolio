-- Workflow Component Search
-- Identifies current workflows that contain a selected application component.
--
-- Portfolio note:
-- This is a sanitized public sample based on SQL I originally wrote for an internal
-- business system. Proprietary schema names, workflow names, identifiers, and business-
-- specific values have been replaced with generic equivalents.
--
-- Key concepts demonstrated:
-- - current-revision filtering
-- - component/configuration tracing
-- - multi-table joins
-- - application configuration analysis

-- This query finds the latest version of each workflow and shows where a specific
-- UI component is being used. I used this when I needed to trace a component
-- across workflows without pulling back older revisions.

SELECT DISTINCT
    latest.RevisionNumber AS LatestRevision,
    grp.GroupName AS WorkflowGroup,
    wf.WorkflowName,
    wf.WorkflowId,
    comp.ComponentName,
    rev.ModifiedByUserId

FROM WorkflowRevision rev WITH (NOLOCK)

-- Get only the most recent revision for each workflow.
INNER JOIN
(
    SELECT
        WorkflowId,
        MAX(RevisionNumber) AS RevisionNumber
    FROM WorkflowRevision
    GROUP BY WorkflowId
) latest
    ON rev.WorkflowId = latest.WorkflowId
    AND rev.RevisionNumber = latest.RevisionNumber

-- Join the workflow itself so I can return the workflow name and ID.
JOIN Workflow wf WITH (NOLOCK)
    ON wf.WorkflowId = latest.WorkflowId

-- Connect the latest workflow revision to its individual nodes.
JOIN WorkflowRevisionNode wrn WITH (NOLOCK)
    ON wrn.WorkflowRevisionId = rev.WorkflowRevisionId

-- These joins let me follow the node into its question/configuration and
-- identify the UI component assigned to it.
LEFT JOIN WorkflowNodeAttribute wna WITH (NOLOCK)
    ON wna.WorkflowNodeId = wrn.WorkflowNodeId

JOIN WorkflowQuestion ques WITH (NOLOCK)
    ON ques.WorkflowNodeId = wrn.WorkflowNodeId

JOIN UIComponent comp WITH (NOLOCK)
    ON comp.ComponentKey = ques.ComponentKey

-- Optional diagnostic joins. I kept these because they were useful when I
-- needed to trace deeper configuration or expression data behind a node.
LEFT JOIN NodeAttribute attr WITH (NOLOCK)
    ON attr.AttributeId = wna.AttributeId

LEFT JOIN RuleExpression expr WITH (NOLOCK)
    ON expr.ExpressionId = attr.ExpressionId

LEFT JOIN ExpressionParameter param WITH (NOLOCK)
    ON param.ExpressionParameterId = expr.FirstParameterId
    -- The second parameter can be joined here instead when that is what I need to trace.

LEFT JOIN ConfigurationValue cfg WITH (NOLOCK)
    ON cfg.ConfigurationValueId = param.ValueConfigurationId

-- Tie the workflow back to the group/program it belongs to.
LEFT JOIN WorkflowGroupMap wgm WITH (NOLOCK)
    ON wgm.WorkflowId = rev.WorkflowId

LEFT JOIN WorkflowGroup grp WITH (NOLOCK)
    ON grp.WorkflowGroupId = wgm.WorkflowGroupId

WHERE
    -- Use this filter instead when I want to see every component inside one workflow.
    -- wf.WorkflowName LIKE '%Example Workflow%'

    -- Use this filter when I want to find every workflow containing a specific component.
    comp.ComponentName LIKE '%example_component%'

    -- Leave archived/test groups out of the results.
    AND grp.GroupName NOT IN ('Archived Workflows', 'Training/Test');