-- Linked Workflow Search
-- Troubleshooting query for tracing parent/linked workflow relationships and latest revisions.
--
-- Portfolio note: This public sample is based on code I originally wrote for an
-- internal business tool. Proprietary names, endpoints, identifiers, sample data,
-- and environment-specific values have been replaced with generic equivalents.
-- Search value used to find a workflow by name.
-- I use wildcards here so I can search using only part of the workflow name.
DECLARE @searchTerm varchar(255)
SET @searchTerm = '%Sample Workflow%'



SELECT DISTINCT
    '' AS Branches

    ,pr.Rev AS Max_Rev
    ,grp.Name AS WorkflowGroup
    ,wf.Name AS ParentWorkflowName
    ,wf.WorkflowId
    ,wn.WorkflowNodeId
    ,br.BranchId
    --,tk.TokenKey
    ,di.DataItemKey
    --,br.ChildWorkflowNodeId
    ,wf2.Name AS LinkedWorkflowName
    --,di_value.DefaultValue

    -- These extra fields can be uncommented when I need to troubleshoot
    -- how a value, token, source, or expression is configured on a node.
    --,idx.Name AS [Index]
    --,scp.Name AS Scope
    --,loc.Name AS [Source]
    --,di.DataItemKey AS Source_Key
    --,di.DefaultValue
    --,obs.[Key] AS DeterminationValue
    --,expr_value.DefaultValue
    --,expr.ExpressionId
    --,expr.ArgumentOneId
    ,rev.UserID

FROM WorkflowRevision rev (NOLOCK)

-- Get only the newest revision for each workflow so I am not
-- troubleshooting an older version that is no longer current.
INNER JOIN
(
    SELECT WorkflowId, MAX(RevisionNumber) AS Rev
    FROM WorkflowRevision
    GROUP BY WorkflowId
) pr
    ON rev.WorkflowId = pr.WorkflowId
    AND rev.RevisionNumber = pr.Rev

-- Join the current workflow revision back to the main workflow record.
JOIN Workflow wf (NOLOCK)
    ON wf.WorkflowId = pr.WorkflowId

-- Follow the workflow node and branch relationships so I can see
-- how one workflow is connected to another workflow.
JOIN WorkflowNode wn (NOLOCK)
    ON wn.WorkflowNodeId = rev.WorkflowRevisionId
LEFT JOIN WorkflowNodeLink wnl
    ON wnl.WorkflowNodeId = wn.WorkflowNodeId
JOIN Branch br
    ON br.ChildWorkflowNodeId = wn.WorkflowNodeId
JOIN BranchCondition bc
    ON bc.BranchId = br.BranchId
LEFT JOIN WorkflowNodeLink wnl_child
    ON wnl_child.WorkflowNodeId = br.ChildWorkflowNodeId
JOIN Workflow wf2
    ON wf2.WorkflowId = wnl.WorkflowId

-- These joins pull supporting configuration data. I kept them in the
-- query because they are useful when I need to dig deeper into why a
-- workflow is linked or how a branch condition is being evaluated.
LEFT JOIN Token tk (NOLOCK)
    ON tk.WorkflowNodeId = wn.WorkflowNodeId
LEFT JOIN DataItem di (NOLOCK)
    ON di.DataItemId = bc.DataItemId
LEFT JOIN DataItem di_value
    ON di_value.DataItemId = bc.ValueDataItemId
LEFT JOIN WorkflowNodeObservation wno (NOLOCK)
    ON wno.WorkflowNodeId = wn.WorkflowNodeId
LEFT JOIN Observation obs (NOLOCK)
    ON obs.ObservationId = wno.ObservationId
LEFT JOIN [Expression] expr (NOLOCK)
    ON expr.ExpressionId = obs.ExpressionId
LEFT JOIN ExpressionArgument expr_arg (NOLOCK)
    ON expr_arg.ExpressionArgumentId = expr.ArgumentOneId
LEFT JOIN DataItem expr_value
    ON expr_value.DataItemId = expr_arg.ValueDataItemId

-- Tie the workflow back to the larger group/program it belongs to.
LEFT JOIN WorkflowGroupLink wgl (NOLOCK)
    ON wgl.WorkflowId = rev.WorkflowId
LEFT JOIN WorkflowGroup grp (NOLOCK)
    ON grp.WorkflowGroupId = wgl.WorkflowGroupId

-- Additional lookup tables used when I need more detail about
-- where a data item comes from or how it is scoped.
LEFT JOIN DataItemIndex idx (NOLOCK)
    ON idx.DataItemIndexId = di.DataItemIndexId
LEFT JOIN DataItemScope scp (NOLOCK)
    ON scp.DataItemScopeId = di.DataItemScopeId
LEFT JOIN DataItemLocation loc (NOLOCK)
    ON loc.DataItemLocationId = di.DataItemLocationId


WHERE
    -- Use this line instead if I want to see every workflow linked FROM
    -- the workflow I searched for.
    --wf.Name LIKE @searchTerm

    -- Use this line when I want to find every workflow that links TO
    -- the workflow I searched for.
    wf2.Name LIKE @searchTerm

    -- Leave archived/retired workflow groups out of the results.
    AND grp.[Name] NOT IN ('Archived Workflows')

ORDER BY WorkflowNodeId DESC