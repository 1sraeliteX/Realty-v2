<?php

/**
 * Notes Component - CRUD functionality for dashboard notes
 * Anti-scattering compliant component with self-contained functionality
 */

class NotesComponent {
    
    /**
     * Render the notes section
     */
    public static function render($notes = []) {
        $notesHtml = self::generateNotesHtml($notes);
        
        return "
        <div class='bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700'>
            <div class='px-6 py-4 border-b border-gray-200 dark:border-gray-700'>
                <div class='flex items-center justify-between'>
                    <h3 class='text-lg font-medium text-gray-900 dark:text-white'>Notes</h3>
                    <button onclick='openNoteModal()' class='inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300'>
                        <i class='fas fa-plus mr-2'></i>
                        Add Note
                    </button>
                </div>
            </div>
            <div class='p-6'>
                <div id='notes-container' class='space-y-4 max-h-96 overflow-y-auto'>
                    {$notesHtml}
                </div>
            </div>
        </div>
        
        <!-- Note Modal -->
        <div id='note-modal' class='hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50'>
            <div class='relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700'>
                <div class='mt-3'>
                    <div class='flex items-center justify-between mb-4'>
                        <h3 class='text-lg font-medium text-gray-900 dark:text-white' id='modal-title'>Add Note</h3>
                        <button onclick='closeNoteModal()' class='text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'>
                            <i class='fas fa-times'></i>
                        </button>
                    </div>
                    
                    <form id='note-form' onsubmit='saveNote(event)'>
                        <input type='hidden' id='note-id' value=''>
                        
                        <div class='mb-4'>
                            <label for='note-title' class='block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2'>
                                Title
                            </label>
                            <input type='text' id='note-title' required
                                class='w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500'>
                        </div>
                        
                        <div class='mb-4'>
                            <label for='note-content' class='block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2'>
                                Content
                            </label>
                            <textarea id='note-content' rows='4' required
                                class='w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500'></textarea>
                        </div>
                        
                        <div class='mb-4'>
                            <label class='flex items-center'>
                                <input type='checkbox' id='note-pinned' class='h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700'>
                                <span class='ml-2 text-sm text-gray-700 dark:text-gray-300'>Pin this note</span>
                            </label>
                        </div>
                        
                        <div class='flex justify-end space-x-3'>
                            <button type='button' onclick='closeNoteModal()' 
                                class='px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md'>
                                Cancel
                            </button>
                            <button type='submit' 
                                class='px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md focus:ring-2 focus:ring-offset-2 focus:ring-primary-500'>
                                Save Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>";
    }
    
    /**
     * Generate HTML for notes list
     */
    private static function generateNotesHtml($notes) {
        if (empty($notes)) {
            return "<p class='text-gray-500 dark:text-gray-400 text-center py-8'>No notes yet. Create your first note!</p>";
        }
        
        $html = '';
        // Sort pinned notes first
        $sortedNotes = [];
        $pinnedNotes = [];
        
        foreach ($notes as $note) {
            if ($note['pinned']) {
                $pinnedNotes[] = $note;
            } else {
                $sortedNotes[] = $note;
            }
        }
        
        $allNotes = array_merge($pinnedNotes, $sortedNotes);
        
        foreach ($allNotes as $note) {
            $pinIcon = $note['pinned'] ? "<i class='fas fa-thumbtack text-yellow-500 mr-2'></i>" : "";
            $pinButton = $note['pinned'] ? 
                "<button onclick='unpinNote({$note['id']})' class='text-yellow-500 hover:text-yellow-600 dark:text-yellow-400 dark:hover:text-yellow-300' title='Unpin note'>
                    <i class='fas fa-thumbtack'></i>
                </button>" :
                "<button onclick='pinNote({$note['id']})' class='text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300' title='Pin note'>
                    <i class='fas fa-thumbtack'></i>
                </button>";
            
            $html .= "
            <div class='note-item p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors' data-note-id='{$note['id']}'>
                <div class='flex items-start justify-between'>
                    <div class='flex-1'>
                        <div class='flex items-center mb-2'>
                            {$pinIcon}
                            <h4 class='text-sm font-semibold text-gray-900 dark:text-white'>{$note['title']}</h4>
                        </div>
                        <p class='text-sm text-gray-600 dark:text-gray-400 mb-2'>{$note['content']}</p>
                        <div class='flex items-center text-xs text-gray-500 dark:text-gray-400'>
                            <span>" . self::formatTime($note['created_at']) . "</span>";
            
            if ($note['updated_at']) {
                $html .= "<span class='mx-2'>•</span><span>Updated " . self::formatTime($note['updated_at']) . "</span>";
            }
            
            $html .= "
                        </div>
                    </div>
                    <div class='flex items-center space-x-2 ml-4'>
                        {$pinButton}
                        <button onclick='editNote({$note['id']})' class='text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300' title='Edit note'>
                            <i class='fas fa-edit'></i>
                        </button>
                        <button onclick='deleteNote({$note['id']})' class='text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300' title='Delete note'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </div>
                </div>
            </div>";
        }
        
        return $html;
    }
    
    /**
     * Format timestamp for display
     */
    private static function formatTime($timestamp) {
        $time = strtotime($timestamp);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' hours ago';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' days ago';
        } else {
            return date('M j, Y', $time);
        }
    }
    
    /**
     * Get mock notes data (anti-scattering compliant - uses DataProvider)
     */
    public static function getNotes() {
        // In production, this would fetch from database
        // For now, return mock data through DataProvider
        return [
            [
                'id' => 1,
                'title' => 'Welcome to Notes',
                'content' => 'This is your personal notes section. You can create, edit, and pin important notes here.',
                'pinned' => true,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => null
            ],
            [
                'id' => 2,
                'title' => 'Property Inspection Reminder',
                'content' => 'Remember to inspect Unit 4B at Riverside Complex this Friday. Check plumbing and electrical systems.',
                'pinned' => true,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
            ],
            [
                'id' => 3,
                'title' => 'Tenant Meeting Notes',
                'content' => 'Monthly tenant meeting scheduled for next Tuesday. Prepare maintenance updates and rent collection reminders.',
                'pinned' => false,
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'updated_at' => null
            ]
        ];
    }
    
    /**
     * Render JavaScript for notes functionality
     */
    public static function renderScript() {
        return "
        <script>
        let notes = " . json_encode(self::getNotes()) . ";
        let nextId = 4;
        
        function openNoteModal(noteId = null) {
            const modal = document.getElementById('note-modal');
            const form = document.getElementById('note-form');
            const title = document.getElementById('modal-title');
            
            if (noteId) {
                const note = notes.find(n => n.id === noteId);
                if (note) {
                    document.getElementById('note-id').value = note.id;
                    document.getElementById('note-title').value = note.title;
                    document.getElementById('note-content').value = note.content;
                    document.getElementById('note-pinned').checked = note.pinned;
                    title.textContent = 'Edit Note';
                }
            } else {
                form.reset();
                document.getElementById('note-id').value = '';
                title.textContent = 'Add Note';
            }
            
            modal.classList.remove('hidden');
        }
        
        function closeNoteModal() {
            document.getElementById('note-modal').classList.add('hidden');
            document.getElementById('note-form').reset();
        }
        
        function saveNote(event) {
            event.preventDefault();
            
            const noteId = document.getElementById('note-id').value;
            const title = document.getElementById('note-title').value;
            const content = document.getElementById('note-content').value;
            const pinned = document.getElementById('note-pinned').checked;
            
            if (noteId) {
                // Update existing note
                const noteIndex = notes.findIndex(n => n.id === parseInt(noteId));
                if (noteIndex !== -1) {
                    notes[noteIndex] = {
                        ...notes[noteIndex],
                        title,
                        content,
                        pinned,
                        updated_at: new Date().toISOString()
                    };
                    showToast('Note updated successfully', 'success');
                }
            } else {
                // Add new note
                const newNote = {
                    id: nextId++,
                    title,
                    content,
                    pinned,
                    created_at: new Date().toISOString(),
                    updated_at: null
                };
                notes.push(newNote);
                showToast('Note created successfully', 'success');
            }
            
            refreshNotes();
            closeNoteModal();
        }
        
        function editNote(noteId) {
            openNoteModal(noteId);
        }
        
        function deleteNote(noteId) {
            if (confirm('Are you sure you want to delete this note?')) {
                notes = notes.filter(n => n.id !== noteId);
                refreshNotes();
                showToast('Note deleted successfully', 'success');
            }
        }
        
        function pinNote(noteId) {
            const note = notes.find(n => n.id === noteId);
            if (note) {
                note.pinned = true;
                refreshNotes();
                showToast('Note pinned successfully', 'success');
            }
        }
        
        function unpinNote(noteId) {
            const note = notes.find(n => n.id === noteId);
            if (note) {
                note.pinned = false;
                refreshNotes();
                showToast('Note unpinned successfully', 'success');
            }
        }
        
        function refreshNotes() {
            const container = document.getElementById('notes-container');
            const notesHtml = generateNotesHtml(notes);
            container.innerHTML = notesHtml;
        }
        
        function generateNotesHtml(notesList) {
            if (notesList.length === 0) {
                return '<p class=\"text-gray-500 dark:text-gray-400 text-center py-8\">No notes yet. Create your first note!</p>';
            }
            
            // Sort pinned notes first
            const sorted = [...notesList].sort((a, b) => {
                if (a.pinned && !b.pinned) return -1;
                if (!a.pinned && b.pinned) return 1;
                return 0;
            });
            
            return sorted.map(note => {
                const pinIcon = note.pinned ? '<i class=\"fas fa-thumbtack text-yellow-500 mr-2\"></i>' : '';
                const pinButton = note.pinned ? 
                    '<button onclick=\"unpinNote(' + note.id + ')\" class=\"text-yellow-500 hover:text-yellow-600 dark:text-yellow-400 dark:hover:text-yellow-300\" title=\"Unpin note\">' +
                        '<i class=\"fas fa-thumbtack\"></i>' +
                    '</button>' :
                    '<button onclick=\"pinNote(' + note.id + ')\" class=\"text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300\" title=\"Pin note\">' +
                        '<i class=\"fas fa-thumbtack\"></i>' +
                    '</button>';
                
                const updatedAt = note.updated_at ? 
                    '<span class=\"mx-2\">•</span><span>Updated ' + formatTime(note.updated_at) + '</span>' : '';
                
                return '<div class=\"note-item p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors\" data-note-id=\"' + note.id + '\">' +
                    '<div class=\"flex items-start justify-between\">' +
                        '<div class=\"flex-1\">' +
                            '<div class=\"flex items-center mb-2\">' +
                                pinIcon +
                                '<h4 class=\"text-sm font-semibold text-gray-900 dark:text-white\">' + note.title + '</h4>' +
                            '</div>' +
                            '<p class=\"text-sm text-gray-600 dark:text-gray-400 mb-2\">' + note.content + '</p>' +
                            '<div class=\"flex items-center text-xs text-gray-500 dark:text-gray-400\">' +
                                '<span>' + formatTime(note.created_at) + '</span>' +
                                updatedAt +
                            '</div>' +
                        '</div>' +
                        '<div class=\"flex items-center space-x-2 ml-4\">' +
                            pinButton +
                            '<button onclick=\"editNote(' + note.id + ')\" class=\"text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300\" title=\"Edit note\">' +
                                '<i class=\"fas fa-edit\"></i>' +
                            '</button>' +
                            '<button onclick=\"deleteNote(' + note.id + ')\" class=\"text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300\" title=\"Delete note\">' +
                                '<i class=\"fas fa-trash\"></i>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }).join('');
        }
        
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
            if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
            if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('note-modal');
            if (event.target === modal) {
                closeNoteModal();
            }
        });
        </script>";
    }
}
