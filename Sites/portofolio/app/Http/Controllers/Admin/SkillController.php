<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SkillRequest;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::with('category')->orderBy('order')->get();
        return view('admin.skills.index', compact('skills'));
    }

    public function create()
    {
        $categories = SkillCategory::orderBy('order')->get();
        return view('admin.skills.create', compact('categories'));
    }

    public function store(SkillRequest $request)
    {
        $data = $request->validated();
        $source = $data['icon_source'];
        unset($data['icon_source'], $data['icon_url'], $data['icon_upload']);

        $data['icon_url']    = null;
        $data['icon_upload'] = null;

        if ($source === 'url') {
            $data['icon_url'] = $request->input('icon_url');
        } elseif ($source === 'upload') {
            $file = $request->file('icon_upload');
            $name = time() . '_' . $file->getClientOriginalName();
            try {
                $file->move(public_path('images/skills'), $name);
                $data['icon_upload'] = 'images/skills/' . $name;
            } catch (\Throwable $e) {
                return back()->withErrors(['icon_upload' => 'Failed to upload icon: ' . $e->getMessage()]);
            }
        }

        Skill::create($data);
        return redirect()->route('admin.skills.index')->with('success', 'Skill created.');
    }

    public function edit(Skill $skill)
    {
        $categories = SkillCategory::orderBy('order')->get();
        return view('admin.skills.edit', compact('skill', 'categories'));
    }

    public function update(SkillRequest $request, Skill $skill)
    {
        $data = $request->validated();
        $source = $data['icon_source'];
        unset($data['icon_source'], $data['icon_url'], $data['icon_upload']);

        $oldUpload = $skill->icon_upload;

        if ($source === 'url') {
            $this->deleteFile($oldUpload);
            $data['icon_url']    = $request->input('icon_url');
            $data['icon_upload'] = null;
        } elseif ($source === 'upload') {
            $data['icon_url'] = null;
            if ($request->hasFile('icon_upload')) {
                $this->deleteFile($oldUpload);
                $file = $request->file('icon_upload');
                $name = time() . '_' . $file->getClientOriginalName();
                try {
                    $file->move(public_path('images/skills'), $name);
                    $data['icon_upload'] = 'images/skills/' . $name;
                } catch (\Throwable $e) {
                    return back()->withErrors(['icon_upload' => 'Failed to upload icon: ' . $e->getMessage()]);
                }
            }
            // No new file → keep existing icon_upload (don't touch it)
        } elseif ($source === 'none') {
            $this->deleteFile($oldUpload);
            $data['icon_url']    = null;
            $data['icon_upload'] = null;
        }

        $skill->update($data);
        return redirect()->route('admin.skills.index')->with('success', 'Skill updated.');
    }

    public function destroy(Skill $skill)
    {
        $this->deleteFile($skill->icon_upload);
        $skill->delete();
        return redirect()->route('admin.skills.index')->with('success', 'Skill deleted.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('ids', []) as $i => $id) {
            Skill::where('id', $id)->update(['order' => $i]);
        }
        return response()->json(['ok' => true]);
    }

    private function deleteFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            try { unlink(public_path($path)); } catch (\Throwable $e) {}
        }
    }
}
