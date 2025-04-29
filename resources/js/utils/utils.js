import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import TurndownService from 'turndown';

/**
 * Format timestamp for display
 */
export const formatTimestamp = (timestamp) => {
    const date = new Date(timestamp);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

/**
 * Export notes to PDF without html2pdf.js
 */
export const exportToPdf = async (notes, lessonTitle) => {
    const container = document.createElement('div');
    container.className = 'pdf-container';
    container.style.padding = '20px';
    container.style.width = '800px'; // Fixed width for consistent rendering

    // Title
    const title = document.createElement('h1');
    title.textContent = `Notes for: ${lessonTitle}`;
    title.style.fontSize = '24px';
    title.style.marginBottom = '20px';
    title.style.color = '#6d28d9';
    container.appendChild(title);

    // Notes
    notes.forEach((note) => {
        const noteElement = document.createElement('div');
        noteElement.style.marginBottom = '20px';
        noteElement.style.padding = '15px';
        noteElement.style.borderRadius = '8px';
        noteElement.style.backgroundColor = '#f9fafb';
        noteElement.style.border = '1px solid #e5e7eb';

        const timestamp = document.createElement('div');
        timestamp.textContent = formatTimestamp(note.timestamp);
        timestamp.style.fontSize = '12px';
        timestamp.style.color = '#6b7280';
        timestamp.style.marginBottom = '8px';

        const content = document.createElement('div');
        content.innerHTML = note.formattedContent || note.content;

        noteElement.appendChild(timestamp);
        noteElement.appendChild(content);
        container.appendChild(noteElement);
    });

    // Append offscreen for rendering
    container.style.position = 'absolute';
    container.style.top = '-9999px';
    document.body.appendChild(container);

    // Render to canvas
    const canvas = await html2canvas(container, { scale: 2 });
    const imgData = canvas.toDataURL('image/png');

    const pdf = new jsPDF('p', 'mm', 'a4');
    const imgProps = pdf.getImageProperties(imgData);
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
    pdf.save(`${lessonTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_notes.pdf`);

    // Cleanup
    document.body.removeChild(container);
};

/**
 * Export notes to Markdown
 */
export const exportToMarkdown = (notes, lessonTitle) => {
    const turndownService = new TurndownService({
        headingStyle: 'atx',
        codeBlockStyle: 'fenced',
    });

    turndownService.addRule('underline', {
        filter: ['u'],
        replacement: (content) => `<u>${content}</u>`,
    });

    let markdown = `# Notes for: ${lessonTitle}\n\n`;

    notes.forEach((note, index) => {
        markdown += `## Note ${index + 1} - ${formatTimestamp(note.timestamp)}\n\n`;

        const noteContent = note.formattedContent || note.content;
        const markdownContent = turndownService.turndown(noteContent);

        markdown += `${markdownContent}\n\n`;

        if (index < notes.length - 1) {
            markdown += '---\n\n';
        }
    });

    const blob = new Blob([markdown], { type: 'text/markdown' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${lessonTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_notes.md`;
    document.body.appendChild(link);
    link.click();

    setTimeout(() => {
        URL.revokeObjectURL(url);
        document.body.removeChild(link);
    }, 100);
};
