<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Constants\EmailActions;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateAssignment;
use App\Models\EmailWidget;
use App\Services\EmailTemplateService;
use App\Services\EmailVariableResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the email templates.
     */
    public function index(): View
    {
        $templates = EmailTemplate::query()
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate(20);

        $widgetsCount = EmailWidget::count();
        $activeWidgetsCount = EmailWidget::where('is_active', true)->count();

        // Check for multiple active templates per action
        $activeTemplatesByAction = EmailTemplate::query()
            ->where('is_active', true)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->having('count', '>', 1)
            ->pluck('count', 'action')
            ->toArray();

        // Get which template is currently being used for each action
        $templateService = app(EmailTemplateService::class);
        $activeActions = array_keys($activeTemplatesByAction);
        $currentlyUsedTemplates = [];
        
        foreach ($activeActions as $action) {
            $usedTemplate = $templateService->getTemplateForAction($action);
            if ($usedTemplate) {
                $currentlyUsedTemplates[$action] = $usedTemplate->id;
            }
        }

        return view('admin.email-templates.index', compact(
            'templates', 
            'widgetsCount', 
            'activeWidgetsCount',
            'activeTemplatesByAction',
            'currentlyUsedTemplates'
        ));
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create(): View
    {
        $actions = $this->getEmailActions();
        $widgets = EmailWidget::query()
            ->active()
            ->ordered()
            ->get()
            ->groupBy('category');

        return view('admin.email-templates.create', compact('actions', 'widgets'));
    }

    /**
     * Store a newly created email template in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'action' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'template_type' => ['required', 'in:override,default'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);

        EmailTemplate::create($data);

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Display the specified email template.
     */
    public function show(int $id): View
    {
        $template = EmailTemplate::with(['creator', 'assignments'])->findOrFail($id);

        return view('admin.email-templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(int $id): View
    {
        $template = EmailTemplate::findOrFail($id);

        $actions = $this->getEmailActions();
        $widgets = EmailWidget::query()
            ->active()
            ->ordered()
            ->get()
            ->groupBy('category');

        return view('admin.email-templates.edit', compact('template', 'actions', 'widgets'));
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $template = EmailTemplate::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'action' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'template_type' => ['required', 'in:override,default'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $template->update($data);

        return redirect()
            ->route('admin.email-templates.edit', $template->id)
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Remove the specified email template from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    /**
     * Preview the rendered template with sample data.
     */
    public function preview(
        Request $request,
        int $id,
        EmailTemplateService $templateService,
        EmailVariableResolver $variableResolver
    ): View {
        $template = EmailTemplate::findOrFail($id);

        // For now, allow passing arbitrary JSON data for preview.
        $rawData = $request->input('data', []);
        $data = is_array($rawData) ? $rawData : (json_decode($rawData, true) ?: []);

        $body = $templateService->renderTemplate($template, $data);
        $body = $variableResolver->resolve($body, $data);

        return view('admin.email-templates.preview', [
            'template' => $template,
            'renderedBody' => $body,
            'data' => $data,
        ]);
    }

    /**
     * Assign this template to an action.
     */
    public function assign(Request $request, int $id): RedirectResponse
    {
        $template = EmailTemplate::findOrFail($id);

        $data = $request->validate([
            'action' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // Optionally deactivate other assignments for this action when creating a new one
        EmailTemplateAssignment::where('action', $data['action'])
            ->update(['is_active' => false]);

        EmailTemplateAssignment::create([
            'action' => $data['action'],
            'template_id' => $template->id,
            'is_active' => $data['is_active'],
        ]);

        return redirect()
            ->route('admin.email-templates.show', $template->id)
            ->with('success', 'Template assigned successfully.');
    }

    /**
     * Get available variables for a given action (AJAX).
     */
    public function getVariables(string $action, EmailTemplateService $templateService)
    {
        $variables = $templateService->getAvailableVariables($action);

        return response()->json([
            'action' => $action,
            'variables' => $variables,
        ]);
    }

    /**
     * Display a listing of all email widgets.
     */
    public function widgets(): View
    {
        $widgets = EmailWidget::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('admin.email-templates.widgets', compact('widgets'));
    }

    /**
     * Get the list of available email actions for dropdowns.
     */
    protected function getEmailActions(): array
    {
        return [
            EmailActions::INSTRUCTION_REQUEST => 'Instruction Request',
            EmailActions::WELCOME_PACK => 'Welcome Pack',
            EmailActions::POST_VALUATION => 'Post Valuation',
            EmailActions::VALUATION_REQUEST => 'Valuation Request',
            EmailActions::VALUATION_LOGIN_CREDENTIALS => 'Valuation Login Credentials',
            EmailActions::VIEWING_REQUEST => 'Viewing Request',
            EmailActions::VIEWING_CONFIRMED => 'Viewing Confirmed',
            EmailActions::VIEWING_ASSIGNED => 'Viewing Assigned',
            EmailActions::NEW_OFFER => 'New Offer',
            EmailActions::OFFER_DECISION => 'Offer Decision',
            EmailActions::OFFER_AMOUNT_RELEASED => 'Offer Amount Released',
            EmailActions::OFFER_DISCUSSION_REQUEST => 'Offer Discussion Request',
            EmailActions::MEMORANDUM_OF_SALE => 'Memorandum of Sale',
            EmailActions::MEMORANDUM_PENDING_INFO => 'Memorandum Pending Info',
            EmailActions::PROPERTY_STATUS_CHANGED => 'Property Status Changed',
            EmailActions::PVA_CREATED => 'PVA Created',
        ];
    }
}


